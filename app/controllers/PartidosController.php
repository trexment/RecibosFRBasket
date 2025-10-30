<?php

class PartidosController
{
    private $db;

    public function __construct()
    {
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../core/Database.php';
        require_once __DIR__ . '/../helpers/session_guard.php';
        require_once __DIR__ . '/../views/partials/alerts.php';
        SessionGuard::check();
        $this->db = Database::getInstance();
    }

    // ==========================================================
    // 📌 Obtener temporada activa
    // ==========================================================
    private function getTemporadaActiva()
    {
        $st = $this->db->query("SELECT * FROM temporadas WHERE activa = 1 LIMIT 1");
        return $st->fetch(PDO::FETCH_ASSOC);
    }

    // ==========================================================
    // 🏠 INDEX
    // ==========================================================
    public function index()
    {
        $usuarioId = $_SESSION['usuario_id'];
        $temporadaActiva = $this->getTemporadaActiva();
        $temporadas = $this->db->query("SELECT * FROM temporadas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $categorias = $this->db->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

        $temporadaSeleccionada = $_GET['temporada_id'] ?? ($temporadaActiva['id'] ?? null);
        $categoriaSeleccionada = $_GET['categoria_id'] ?? '';
        $fechaDesde = $_GET['desde'] ?? '';
        $fechaHasta = $_GET['hasta'] ?? '';

        $porPagina = 10;
        $paginaActual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) && $_GET['pagina'] > 0 ? (int)$_GET['pagina'] : 1;
        $offset = ($paginaActual - 1) * $porPagina;

        $sql = "
            SELECT p.*, c.nombre AS categoria_nombre,
                   el.nombre AS equipo_local, ev.nombre AS equipo_visitante
            FROM partidos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            LEFT JOIN equipos el ON el.id = p.equipo_local_id
            LEFT JOIN equipos ev ON ev.id = p.equipo_visitante_id
            WHERE p.usuario_id = :u
        ";

        $params = [':u' => $usuarioId];
        if ($temporadaSeleccionada) {
            $sql .= " AND p.temporada_id = :t";
            $params[':t'] = (int)$temporadaSeleccionada;
        }
        if (!empty($categoriaSeleccionada)) {
            $sql .= " AND p.categoria_id = :c";
            $params[':c'] = (int)$categoriaSeleccionada;
        }
        if (!empty($fechaDesde)) {
            $sql .= " AND p.fecha >= :desde";
            $params[':desde'] = $fechaDesde;
        }
        if (!empty($fechaHasta)) {
            $sql .= " AND p.fecha <= :hasta";
            $params[':hasta'] = $fechaHasta;
        }

        $sqlCount = "SELECT COUNT(*) FROM (" . $sql . ") AS total";
        $stmtCount = $this->db->prepare($sqlCount);
        $stmtCount->execute($params);
        $totalRegistros = (int)$stmtCount->fetchColumn();
        $totalPaginas = ceil($totalRegistros / $porPagina);

        $sql .= " ORDER BY p.fecha DESC LIMIT :offset, :limit";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$porPagina, PDO::PARAM_INT);
        $stmt->execute();

        $partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../views/partidos/index.php';
    }
    // ==========================================================
    // ✏️ EDITAR PARTIDO
    // ==========================================================
    public function editar($id)
    {
        SessionGuard::check();
        $usuario_id = $_SESSION['usuario_id'];

        // 🔹 Obtener temporada activa
        $temporada_activa = $this->db->query("SELECT * FROM temporadas WHERE activa = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if (!$temporada_activa) {
            $_SESSION['flash_error'] = "⚠️ No hay temporada activa.";
            header('Location: ' . BASE_URL . 'temporadas');
            exit;
        }

        // 🔹 Obtener partido
        $stmt = $this->db->prepare("
        SELECT p.*, c.nombre AS categoria_nombre
        FROM partidos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE p.id = :id
        LIMIT 1
    ");
        $stmt->execute([':id' => $id]);
        $partido = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$partido) {
            $_SESSION['flash_error'] = "❌ Partido no encontrado.";
            header('Location: ' . BASE_URL . 'partidos');
            exit;
        }

        // 🔹 Si el partido no tiene categoría, la calculamos desde el equipo local
        if (empty($partido['categoria_id']) && !empty($partido['equipo_local_id'])) {
            $stCat = $this->db->prepare("
            SELECT c.id AS categoria_id, c.nombre AS categoria_nombre
            FROM equipos e
            LEFT JOIN categorias c ON e.categoria_id = c.id
            WHERE e.id = :id
            LIMIT 1
        ");
            $stCat->execute([':id' => $partido['equipo_local_id']]);
            $equipoCat = $stCat->fetch(PDO::FETCH_ASSOC);

            if ($equipoCat) {
                $partido['categoria_id'] = $equipoCat['categoria_id'];
                $partido['categoria_nombre'] = $equipoCat['categoria_nombre'];
            }
        }

        // 🔹 Cargar categorías y equipos
        $categorias = $this->db->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
        $stmtEquipos = $this->db->prepare("
        SELECT e.*, c.nombre AS categoria_nombre 
        FROM equipos e
        LEFT JOIN categorias c ON e.categoria_id = c.id
        WHERE e.temporada_id = :t
        ORDER BY e.nombre ASC
    ");
        $stmtEquipos->execute([':t' => $temporada_activa['id']]);
        $equipos = $stmtEquipos->fetchAll(PDO::FETCH_ASSOC);

        // 🔹 Crear abreviatura de categoría
        foreach ($equipos as &$eq) {
            $cat = strtoupper(trim($eq['categoria_nombre'] ?? ''));
            $abbr = '';
            foreach (explode(' ', $cat) as $w) {
                if (mb_strlen($w) > 2) $abbr .= mb_substr($w, 0, 1);
            }
            $eq['nombre_mostrado'] = "{$eq['nombre']} ({$abbr})";
        }

        // 🔹 Roles disponibles (de la tabla tarifas)
        $roles = $this->db->prepare("SELECT DISTINCT rol FROM tarifas WHERE temporada_id = :t ORDER BY rol ASC");
        $roles->execute([':t' => $temporada_activa['id']]);
        $roles = $roles->fetchAll(PDO::FETCH_COLUMN);

        // 🔹 Precio por km
        $precio_km = $this->db->prepare("SELECT precio_km FROM desplazamientos WHERE temporada_id = :t AND activo = 1 LIMIT 1");
        $precio_km->execute([':t' => $temporada_activa['id']]);
        $precio_km = $precio_km->fetchColumn() ?: 0.26;

        // ======================================================
        // 📝 Si se envía el formulario
        // ======================================================
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fecha = $_POST['fecha'] ?? '';
            $jornada = $_POST['jornada'] ?? '';
            $categoria_id = $_POST['categoria_id'] ?? '';
            $equipo_local_id = $_POST['equipo_local_id'] ?? null;
            $equipo_visitante_id = $_POST['equipo_visitante_id'] ?? null;
            $rol = $_POST['rol'] ?? '';
            $usa_tablet = isset($_POST['usa_tablet']) ? 1 : 0;
            $km = floatval($_POST['km'] ?? 0);
            $importe_desplazamiento = floatval($_POST['importe_desplazamiento'] ?? 0);
            $dieta = floatval($_POST['dieta'] ?? 0);

            if (!$fecha || !$equipo_local_id || !$equipo_visitante_id || !$rol) {
                $_SESSION['flash_error'] = "⚠️ Debes completar todos los campos obligatorios.";
                header('Location: ' . BASE_URL . 'partidos/editar/' . $id);
                exit;
            }

            // 🔹 Calcular importe por tarifa
            $stmtTarifa = $this->db->prepare("
            SELECT importe 
            FROM tarifas 
            WHERE categoria_id = :categoria 
              AND temporada_id = :temporada 
              AND rol = :rol
            LIMIT 1
        ");
            $stmtTarifa->execute([
                ':categoria' => $categoria_id,
                ':temporada' => $temporada_activa['id'],
                ':rol' => $rol
            ]);
            $importe = $stmtTarifa->fetchColumn() ?: 0;

            // 🔹 Sumar 1€ si es oficial y usa tablet
            if (in_array($rol, ['oficial', 'oficial_solo']) && $usa_tablet) {
                $importe += 1;
            }

            // 🔹 Calcular desplazamiento
            if ($km > 0 && $importe_desplazamiento == 0) {
                $importe_desplazamiento = $km * $precio_km;
            } elseif ($importe_desplazamiento > 0 && $km == 0) {
                $km = $importe_desplazamiento / $precio_km;
            }

            // 🔹 Actualizar partido
            $stmtUpdate = $this->db->prepare("
            UPDATE partidos SET
                fecha = :fecha,
                jornada = :jornada,
                equipo_local_id = :local,
                equipo_visitante_id = :visitante,
                categoria_id = :categoria,
                rol = :rol,
                importe = :importe,
                importe_desplazamiento = :importe_desplazamiento,
                km = :km,
                dieta = :dieta,
                usa_tablet = :usa_tablet
            WHERE id = :id AND usuario_id = :usuario
        ");
            $stmtUpdate->execute([
                ':fecha' => $fecha,
                ':jornada' => $jornada,
                ':local' => $equipo_local_id,
                ':visitante' => $equipo_visitante_id,
                ':categoria' => $categoria_id,
                ':rol' => $rol,
                ':importe' => $importe,
                ':importe_desplazamiento' => $importe_desplazamiento,
                ':km' => $km,
                ':dieta' => $dieta,
                ':usa_tablet' => $usa_tablet,
                ':id' => $id,
                ':usuario' => $usuario_id
            ]);

            $_SESSION['flash_success'] = "✅ Partido actualizado correctamente.";
            header('Location: ' . BASE_URL . 'partidos');
            exit;
        }

        // 🔹 Mostrar vista
        require __DIR__ . '/../views/partidos/editar.php';
    }



    // ==========================================================
    // ➕ CREAR
    // ==========================================================
    public function crear()
    {
        SessionGuard::check();
        $usuarioId = $_SESSION['usuario_id'];

        // Obtener temporada activa
        $temporada = $this->getTemporadaActiva();
        if (!$temporada) {
            $_SESSION['flash_error'] = "No hay temporada activa.";
            header("Location: " . BASE_URL . "temporadas");
            exit;
        }

        // Obtener equipos con categoría y abreviatura
        $stmt = $this->db->prepare("
        SELECT e.*, c.nombre AS categoria_nombre, c.id AS categoria_id
        FROM equipos e
        LEFT JOIN categorias c ON e.categoria_id = c.id
        WHERE e.temporada_id = :t
        ORDER BY e.nombre ASC
    ");
        $stmt->execute([':t' => $temporada['id']]);
        $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Crear abreviatura automática (p.ej. "Junior Masculino" → "JM")
        foreach ($equipos as &$eq) {
            $cat = strtoupper(trim($eq['categoria_nombre'] ?? ''));
            $abbr = '';
            foreach (explode(' ', $cat) as $w) {
                if (mb_strlen($w) > 2) $abbr .= mb_substr($w, 0, 1);
            }
            $eq['nombre_mostrado'] = "{$eq['nombre']} ({$abbr})";
        }

        // Variables necesarias para la vista
        $temporada_activa = $temporada;
        $precio_km = $this->db->query("
        SELECT precio_km FROM desplazamientos 
        WHERE temporada_id = {$temporada['id']} AND activo = 1 
        LIMIT 1
    ")->fetchColumn() ?: 0.26;

        // ===============================================
        // 🚀 PROCESAR FORMULARIO POST
        // ===============================================
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fecha = $_POST['fecha'] ?? '';
            $jornada = $_POST['jornada'] ?? '';
            $categoria_id = $_POST['categoria_id'] ?? '';
            $equipo_local_id = $_POST['equipo_local_id'] ?? '';
            $equipo_visitante_id = $_POST['equipo_visitante_id'] ?? '';
            $rol = $_POST['rol'] ?? '';
            $usa_tablet = isset($_POST['usa_tablet']) ? 1 : 0;
            $km = floatval($_POST['km'] ?? 0);
            $importe_desplazamiento = floatval($_POST['importe_desplazamiento'] ?? 0);
            $dieta = floatval($_POST['dieta'] ?? 0);

            // Validaciones básicas
            if (!$fecha || !$equipo_local_id || !$equipo_visitante_id || !$rol) {
                $_SESSION['flash_error'] = "⚠️ Completa todos los campos obligatorios.";
                header("Location: " . BASE_URL . "partidos/crear");
                exit;
            }

            // Evitar duplicados
            $check = $this->db->prepare("
            SELECT COUNT(*) FROM partidos 
            WHERE usuario_id = :u AND fecha = :f 
              AND equipo_local_id = :el AND equipo_visitante_id = :ev
        ");
            $check->execute([
                ':u' => $usuarioId,
                ':f' => $fecha,
                ':el' => $equipo_local_id,
                ':ev' => $equipo_visitante_id
            ]);

            if ($check->fetchColumn() > 0) {
                $_SESSION['flash_error'] = "⚠️ Ya existe un partido con esos equipos en esa fecha.";
                header("Location: " . BASE_URL . "partidos");
                exit;
            }

            // Calcular importe según tarifa
            $stTarifa = $this->db->prepare("
            SELECT importe 
            FROM tarifas 
            WHERE categoria_id = :c AND temporada_id = :t AND rol = :r 
            LIMIT 1
        ");
            $stTarifa->execute([
                ':c' => $categoria_id,
                ':t' => $temporada['id'],
                ':r' => $rol
            ]);
            $importe = $stTarifa->fetchColumn() ?: 0;

            // Sumar 1€ si es oficial y usa tablet
            if (in_array($rol, ['oficial', 'oficial_solo']) && $usa_tablet) {
                $importe += 1;
            }

            // Calcular desplazamiento (si aplica)
            if ($km > 0 && !$importe_desplazamiento) {
                $importe_desplazamiento = $km * $precio_km;
            }

            // Insertar partido
            $ins = $this->db->prepare("
            INSERT INTO partidos 
                (fecha, jornada, equipo_local_id, equipo_visitante_id, categoria_id, temporada_id, usuario_id, rol, importe, importe_desplazamiento, km, dieta, usa_tablet)
            VALUES 
                (:f, :j, :el, :ev, :c, :t, :u, :r, :imp, :desp, :km, :dieta, :tab)
        ");
            $ins->execute([
                ':f' => $fecha,
                ':j' => $jornada,
                ':el' => $equipo_local_id,
                ':ev' => $equipo_visitante_id,
                ':c' => $categoria_id,
                ':t' => $temporada['id'],
                ':u' => $usuarioId,
                ':r' => $rol,
                ':imp' => $importe,
                ':desp' => $importe_desplazamiento,
                ':km' => $km,
                ':dieta' => $dieta,
                ':tab' => $usa_tablet
            ]);

            $_SESSION['flash_success'] = "✅ Partido creado correctamente.";
            header("Location: " . BASE_URL . "partidos");
            exit;
        }

        // ===============================================
        // 📄 CARGAR VISTA
        // ===============================================
        require __DIR__ . '/../views/partidos/crear.php';
    }



    // ==========================================================
// 📥 IMPORTAR PARTIDOS DESDE CSV
// ==========================================================
    public function importar_csv()
    {
        SessionGuard::check(); // Solo usuarios logueados

        // Obtener temporada activa
        $temporada = $this->getTemporadaActiva();
        if (!$temporada) {
            $_SESSION['flash_error'] = "⚠️ No hay ninguna temporada activa. Activa una antes de importar partidos.";
            header("Location: " . BASE_URL . "partidos");
            exit;
        }

        // === Si es GET → mostrar formulario ===
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once __DIR__ . '/../views/partidos/importar_csv.php';
            return;
        }

        // === Si es POST → procesar archivo ===
        if (empty($_FILES['csv']['tmp_name']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = "❌ No se ha subido ningún archivo CSV válido.";
            header("Location: " . BASE_URL . "partidos/importar_csv");
            exit;
        }

        $fh = fopen($_FILES['csv']['tmp_name'], 'r');
        if (!$fh) {
            $_SESSION['flash_error'] = "❌ No se pudo abrir el archivo CSV.";
            header("Location: " . BASE_URL . "partidos/importar_csv");
            exit;
        }

        // 🧹 Eliminar BOM (UTF-8 con BOM)
        $firstBytes = fread($fh, 3);
        if ($firstBytes !== "\xEF\xBB\xBF") {
            rewind($fh);
        }

        // 🧭 Buscar primera línea no vacía
        $header = null;
        while (($line = fgetcsv($fh, 20000, ';', '"', '\\')) !== false) {
            if (count(array_filter($line)) < 2) continue;
            $header = $line;
            break;
        }

        // 🚨 Validar encabezado
        if (!$header || count($header) < 2) {
            $_SESSION['flash_error'] = "❌ No se detectaron encabezados en el CSV (verifica el delimitador ';' o el formato del archivo).";
            fclose($fh);
            header("Location: " . BASE_URL . "partidos/importar_csv");
            exit;
        }

        // 🔍 Campos esperados
        $map = [
            'fecha'            => -1,
            'jornada'          => -1,
            'categoria'        => -1,
            'equipo_local'     => -1,
            'equipo_visitante' => -1,
            'rol'              => -1,
            'tablet'           => -1
        ];

        // 🔧 Normalizador
        $normalize = function($str) {
            $str = strtolower(trim($str));
            $str = str_replace(['á','é','í','ó','ú','ü','ñ'], ['a','e','i','o','u','u','n'], $str);
            $str = str_replace([' ', '-'], '_', $str);
            $str = preg_replace('/[^a-z0-9_]/', '', $str);
            return $str;
        };

        // 🔍 Equivalencias comunes
        $equivalencias = [
            'categoria'        => ['categoria', 'competicion', 'categoria_1'],
            'fecha'            => ['fecha', 'dia', 'fecha_partido'],
            'equipo_local'     => ['local', 'equipo_local', 'club_local'],
            'equipo_visitante' => ['visitante', 'equipo_visitante', 'club_visitante'],
            'jornada'          => ['jornada', 'num_jornada', 'jorn'],
            'rol'              => ['rol', 'funcion', 'papel'],
            'tablet'           => ['tablet', 'usa_tablet', 'dispositivo']
        ];

        // 🧭 Mapear columnas
        foreach ($header as $i => $col) {
            $colNorm = $normalize($col);
            foreach ($equivalencias as $campo => $variantes) {
                foreach ($variantes as $v) {
                    if ($colNorm === $v) {
                        $map[$campo] = $i;
                    }
                }
            }
        }

        // ✅ Validación mínima
        foreach (['fecha','categoria','equipo_local','equipo_visitante'] as $req) {
            if ($map[$req] === -1) {
                $_SESSION['flash_error'] = "⚠️ El CSV debe incluir al menos las columnas: Fecha, Categoría, Local y Visitante.";
                fclose($fh);
                header("Location: " . BASE_URL . "partidos/importar_csv");
                exit;
            }
        }

        // === Leer filas ===
        $preview = [];
        while (($row = fgetcsv($fh, 2000, ';', '"', '\\')) !== false) {
            if (count($row) < 4) continue; // Saltar líneas vacías

            $preview[] = [
                'fecha'           => $row[$map['fecha']]            ?? '',
                'jornada'         => $row[$map['jornada']]          ?? '',
                'categoria'       => $row[$map['categoria']]        ?? '',
                'equipo_local'    => $row[$map['equipo_local']]     ?? '',
                'equipo_visitante'=> $row[$map['equipo_visitante']] ?? '',
                'rol'             => $row[$map['rol']]              ?? '',
                'tablet'          => $row[$map['tablet']]           ?? ''
            ];
        }
        fclose($fh);

        // Guardar previsualización temporal en sesión
        $_SESSION['csv_preview'] = $preview;

        // Si no hay partidos detectados
        if (empty($preview)) {
            $_SESSION['flash_error'] = "⚠️ No se detectaron partidos válidos en el archivo CSV.";
            header("Location: " . BASE_URL . "partidos/importar_csv");
            exit;
        }

        // Mostrar previsualización
        require_once __DIR__ . '/../views/partidos/importar_csv_confirmar.php';
    }
    // ==========================================================
    // 💾 CONFIRMAR IMPORTACIÓN DESDE CSV
    // ==========================================================
    public function confirmar_importacion_csv()
    {
        SessionGuard::check();

        $db = Database::getInstance();
        $usuario = $_SESSION['usuario'] ?? null;
        if (!$usuario) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        // Verificar temporada activa
        $tempStmt = $db->query("SELECT id FROM temporadas WHERE activa = 1 LIMIT 1");
        $temporada = $tempStmt->fetchColumn();
        if (!$temporada) {
            echo "<div class='alert alert-danger m-3'>❌ No hay temporada activa. Activa una antes de importar partidos.</div>";
            return;
        }

        // Verificar datos recibidos
        if (empty($_POST['importar']) || empty($_POST['partidos'])) {
            echo "<div class='alert alert-warning m-3'>⚠️ No se seleccionaron partidos para importar.</div>";
            return;
        }

        $partidos = $_POST['partidos'];
        $seleccionados = $_POST['importar'];
        $insertados = 0;
        $omitidos = 0;

        foreach ($seleccionados as $i) {
            if (!isset($partidos[$i])) continue;
            $p = $partidos[$i];

            // Normalizar campos
            $fecha = date('Y-m-d', strtotime(str_replace('/', '-', $p['fecha'] ?? '')));
            $jornada = trim($p['jornada'] ?? '');
            $categoria = trim($p['categoria'] ?? '');
            $local = trim($p['equipo_local'] ?? '');
            $visitante = trim($p['equipo_visitante'] ?? '');
            $rol = strtolower(trim($p['rol'] ?? 'oficial'));
            $tablet = isset($p['tablet']) && ($p['tablet'] == 1 || $p['tablet'] === 'sí' || $p['tablet'] === 'si') ? 1 : 0;

            if (!$local || !$visitante || !$categoria || !$fecha) {
                $omitidos++;
                continue;
            }

            // Buscar o crear categoría
            $catStmt = $db->prepare("SELECT id FROM categorias WHERE nombre LIKE :n LIMIT 1");
            $catStmt->execute([':n' => "%$categoria%"]);
            $categoriaId = $catStmt->fetchColumn();

            if (!$categoriaId) {
                $catIns = $db->prepare("INSERT INTO categorias (nombre) VALUES (:n)");
                $catIns->execute([':n' => $categoria]);
                $categoriaId = $db->lastInsertId();
            }

            // Buscar o crear equipos
            $eqStmt = $db->prepare("SELECT id FROM equipos WHERE nombre LIKE :n AND temporada_id = :t LIMIT 1");

            // Local
            $eqStmt->execute([':n' => "%$local%", ':t' => $temporada]);
            $idLocal = $eqStmt->fetchColumn();
            if (!$idLocal) {
                $ins = $db->prepare("INSERT INTO equipos (nombre, categoria_id, temporada_id) VALUES (:n, :c, :t)");
                $ins->execute([':n' => $local, ':c' => $categoriaId, ':t' => $temporada]);
                $idLocal = $db->lastInsertId();
            }

            // Visitante
            $eqStmt->execute([':n' => "%$visitante%", ':t' => $temporada]);
            $idVisitante = $eqStmt->fetchColumn();
            if (!$idVisitante) {
                $ins = $db->prepare("INSERT INTO equipos (nombre, categoria_id, temporada_id) VALUES (:n, :c, :t)");
                $ins->execute([':n' => $visitante, ':c' => $categoriaId, ':t' => $temporada]);
                $idVisitante = $db->lastInsertId();
            }

            // Calcular importe según tarifa
            $stmtTarifa = $db->prepare("
            SELECT importe 
            FROM tarifas 
            WHERE categoria_id = :categoria 
              AND temporada_id = :temporada 
              AND rol = :rol
            LIMIT 1
        ");
            $stmtTarifa->execute([
                ':categoria' => $categoriaId,
                ':temporada' => $temporada,
                ':rol' => $rol
            ]);
            $importe = $stmtTarifa->fetchColumn() ?: 0;

            // Sumar +1 € si es oficial y usa tablet
            if (in_array($rol, ['oficial', 'oficial_solo']) && $tablet) {
                $importe += 1;
            }

            // Evitar duplicados
            $check = $db->prepare("
            SELECT COUNT(*) 
            FROM partidos 
            WHERE fecha = :f 
              AND equipo_local_id = :l 
              AND equipo_visitante_id = :v 
              AND usuario_id = :u
        ");
            $check->execute([
                ':f' => $fecha,
                ':l' => $idLocal,
                ':v' => $idVisitante,
                ':u' => $usuario['id']
            ]);

            if ($check->fetchColumn() > 0) {
                $omitidos++;
                continue;
            }

            // Insertar partido
            $insert = $db->prepare("
            INSERT INTO partidos 
                (fecha, jornada, equipo_local_id, equipo_visitante_id, categoria_id, usuario_id, rol, usa_tablet, temporada_id, importe)
            VALUES 
                (:f, :j, :l, :v, :c, :u, :r, :t, :temp, :imp)
        ");
            $insert->execute([
                ':f' => $fecha,
                ':j' => $jornada,
                ':l' => $idLocal,
                ':v' => $idVisitante,
                ':c' => $categoriaId,
                ':u' => $usuario['id'],
                ':r' => $rol,
                ':t' => $tablet,
                ':temp' => $temporada,
                ':imp' => $importe
            ]);

            $insertados++;
        }

        echo "<div class='alert alert-success m-3'>
        ✅ Se importaron correctamente <b>$insertados</b> partidos.<br>
        ⚠️ Se omitieron <b>$omitidos</b> duplicados o incompletos.<br><br>
        <a href='" . BASE_URL . "partidos' class='btn btn-primary mt-2'>
            <i class='fas fa-arrow-left'></i> Volver al listado
        </a>
    </div>";
    }


    // ==========================================================
// 📥 IMPORTAR DESIGNACIÓN DESDE PDF
// ==========================================================
    public function importar_pdf()
    {
        SessionGuard::check();
        require __DIR__ . '/../views/partidos/importar_pdf.php';
    }

// ==========================================================
// 📊 PROCESAR DESIGNACIÓN PDF (análisis + previsualización)
// ==========================================================
    public function procesar_importacion_pdf()
    {
        SessionGuard::check();

        $usuario = $_SESSION['usuario'];
        $colegiado = $usuario['colegiado'] ?? null;

        if (!$colegiado) {
            echo "<div class='alert alert-warning m-3'>⚠️ Debes configurar tu número de colegiado en <b>Mi Perfil</b> antes de importar designaciones.</div>";
            return;
        }

        if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
            echo "<div class='alert alert-danger m-3'>❌ No se ha subido ningún archivo PDF válido.</div>";
            return;
        }

        // ==============================
        // Cargar Smalot PdfParser manualmente
        // ==============================
        $baseDir = __DIR__ . '/../libraries/Smalot/PdfParser/';
        spl_autoload_register(function ($class) use ($baseDir) {
            $prefix = 'Smalot\\PdfParser\\';
            if (strpos($class, $prefix) !== 0) return;
            $relativeClass = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) require_once $file;
        });

        // ==============================
        // Leer PDF
        // ==============================
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($_FILES['pdf']['tmp_name']);
            $text = strtoupper($pdf->getText());
            $text = preg_replace('/\s+/', ' ', $text);
            file_put_contents(__DIR__ . '/../../debug_texto_pdf.txt', $text);
        } catch (Exception $e) {
            echo "<div class='alert alert-danger m-3'>❌ Error al leer el PDF: {$e->getMessage()}</div>";
            return;
        }

        // ==============================
        // Analizar texto
        // ==============================
        $pattern = '/(\d{4,5})\s+(CR|AN|AP|AA)\s+([A-ZÑÁÉÍÓÚ0-9\(\)\.\-]+(?:\s+(?:MASCULINO|FEMENINO|MIXTO|MINI|INFANTIL|CADETE|JUNIOR|SENIOR))?)\s+([A-ZÑÁÉÍÓÚ0-9\.\s\-]+?)\s+([A-ZÑÁÉÍÓÚ0-9\.\s\-]+?)\s+(\d{1,2}:\d{2})/u';

        preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);
        $partidos = [];

        foreach ($matches as $m) {
            $numPartido = trim($m[1]);
            $rolCodigo = trim($m[2]);
            $categoria = trim($m[3]);
            $equipoLocal = trim($m[4]);
            $equipoVisitante = trim($m[5]);
            $hora = trim($m[6]);

            // Fecha
            if (preg_match('/\b(\d{2}\/\d{2}\/\d{4})\b/', $text, $fechaMatch)) {
                $fecha = $fechaMatch[1];
            } else {
                $fecha = date('d/m/Y');
            }

            // Jornada
            $jornada = 1;
            if (preg_match_all('/\bJORNADA\s*(\d{1,2})\b/', $text, $jornadas)) {
                $jornada = end($jornadas[1]);
            }

            // Detectar rol del usuario
            $rolDetectado = 'oficial';
            $tablet = 0;
            $colegiadoEsc = preg_quote($colegiado, '/');

            if (preg_match("/{$colegiadoEsc}[^A-Z]*(ARBITRO\s+PRINCIPAL|ARBITRO\s+AUXILIAR)/u", $text)) {
                $rolDetectado = 'arbitro';
            } elseif (preg_match("/{$colegiadoEsc}[^A-Z]*(ANOTADOR)/u", $text)) {
                $rolDetectado = 'oficial';
                $tablet = 1; // solo anotador usa tablet
            } elseif (preg_match("/{$colegiadoEsc}[^A-Z]*(CRONOMETRADOR)/u", $text)) {
                $rolDetectado = 'oficial';
                $tablet = 0; // cronometrador no usa tablet
            }

            if ($rolCodigo === 'CR') {
                $tablet = 0;
            }

            $partidos[] = [
                'num_partido'       => $numPartido,
                'fecha'             => $fecha,
                'jornada'           => $jornada,
                'categoria'         => ucwords(strtolower($categoria)),
                'equipo_local'      => ucwords(strtolower($equipoLocal)),
                'equipo_visitante'  => ucwords(strtolower($equipoVisitante)),
                'hora'              => $hora,
                'rol'               => $rolDetectado,
                'tablet'            => $tablet,
            ];
        }

        if (empty($partidos)) {
            echo "<div class='alert alert-warning m-3'>⚠️ No se detectaron partidos válidos en el PDF.<br>
        Verifica el archivo o revisa el contenido de <code>debug_texto_pdf.txt</code>.</div>";
            return;
        }

        // Guardar partidos detectados en sesión y mostrar previsualización
        $_SESSION['partidos_detectados'] = $partidos;
        require __DIR__ . '/../views/partidos/previsualizacion_pdf.php';
    }

// ==========================================================
// 💾 CONFIRMAR IMPORTACIÓN PDF (con corrección manual)
// ==========================================================
    public function confirmar_importacion_pdf_corregida()
    {
        SessionGuard::check();
        $db = Database::getInstance();
        $usuarioId = $_SESSION['usuario_id'] ?? null;
        if (!$usuarioId) {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        // Temporada activa
        $t = $db->query("SELECT id FROM temporadas WHERE activa=1 LIMIT 1")->fetchColumn();
        if (!$t) {
            $_SESSION['flash_error'] = "No hay temporada activa.";
            header("Location: " . BASE_URL . "partidos");
            exit;
        }

        $rows = $_POST['rows'] ?? [];
        if (empty($rows)) {
            $_SESSION['flash_error'] = "No se seleccionó ningún partido.";
            header("Location: " . BASE_URL . "partidos");
            exit;
        }

        $tarifaStmt = $db->prepare("
        SELECT importe FROM tarifas 
        WHERE categoria_id = :c AND temporada_id = :t AND rol = :r LIMIT 1
    ");

        $insertados = 0;
        $omitidos = 0;

        foreach ($rows as $i) {
            $fecha   = $_POST['fecha'][$i] ?? '';
            $jornada = $_POST['jornada'][$i] ?? '';
            $categoriaNombre = trim($_POST['categoria'][$i] ?? '');
            $localNombre     = trim($_POST['equipo_local'][$i] ?? '');
            $visitNombre     = trim($_POST['equipo_visitante'][$i] ?? '');
            $rol             = strtolower(trim($_POST['rol'][$i] ?? 'oficial'));
            $usa_tablet      = isset($_POST['tablet'][$i]) ? 1 : 0;

            if (!$fecha || !$categoriaNombre || !$localNombre || !$visitNombre) {
                $omitidos++;
                continue;
            }

            // Buscar o crear categoría
            $catStmt = $db->prepare("SELECT id FROM categorias WHERE nombre LIKE :n LIMIT 1");
            $catStmt->execute([':n' => "%$categoriaNombre%"]);
            $catId = $catStmt->fetchColumn();

            if (!$catId) {
                $insCat = $db->prepare("INSERT INTO categorias (nombre) VALUES (:n)");
                $insCat->execute([':n' => $categoriaNombre]);
                $catId = $db->lastInsertId();
            }

            // Buscar o crear equipos
            $localId = $this->buscarEquipoId($localNombre, $catId, $t);
            $visitId = $this->buscarEquipoId($visitNombre, $catId, $t);

            // Duplicado
            $dup = $db->prepare("
            SELECT id FROM partidos
            WHERE fecha=:f AND equipo_local_id=:l AND equipo_visitante_id=:v AND usuario_id=:u
        ");
            $dup->execute([':f' => $fecha, ':l' => $localId, ':v' => $visitId, ':u' => $usuarioId]);
            if ($dup->fetch()) {
                $omitidos++;
                continue;
            }

            // Tarifa
            $tarifaStmt->execute([':c' => $catId, ':t' => $t, ':r' => $rol]);
            $importe = (float)($tarifaStmt->fetchColumn() ?: 0);
            if (in_array($rol, ['oficial', 'oficial_solo']) && $usa_tablet) {
                $importe += 1;
            }

            // Insertar partido
            $ins = $db->prepare("
            INSERT INTO partidos
            (fecha, jornada, equipo_local_id, equipo_visitante_id, categoria_id, temporada_id, usuario_id, rol, importe, usa_tablet)
            VALUES (:f,:j,:el,:ev,:c,:t,:u,:r,:imp,:tab)
        ");
            $ins->execute([
                ':f' => $fecha, ':j' => $jornada, ':el' => $localId, ':ev' => $visitId,
                ':c' => $catId, ':t' => $t, ':u' => $usuarioId, ':r' => $rol,
                ':imp' => $importe, ':tab' => $usa_tablet
            ]);

            $insertados++;
        }

        $_SESSION['flash_success'] = "✅ Guardado completado. Insertados: $insertados, omitidos: $omitidos.";
        header("Location: " . BASE_URL . "partidos");
        exit;
    }

// ==========================================================
// 🔧 Métodos auxiliares usados arriba
// ==========================================================
    private function buscarEquipoId($nombre, $categoriaId, $temporadaId)
    {
        $st = $this->db->prepare("SELECT id FROM equipos WHERE nombre LIKE :n AND temporada_id = :t LIMIT 1");
        $st->execute([':n' => "%$nombre%", ':t' => $temporadaId]);
        $id = $st->fetchColumn();

        if (!$id) {
            $ins = $this->db->prepare("INSERT INTO equipos (nombre, categoria_id, temporada_id) VALUES (:n, :c, :t)");
            $ins->execute([':n' => $nombre, ':c' => $categoriaId, ':t' => $temporadaId]);
            $id = $this->db->lastInsertId();
        }
        return $id;
    }


    // ==========================================================
    // ⚙️ AJAX: Categoría / Equipos / Tarifas
    // ==========================================================
    public function getCategoriaNombre()
    {
        $id=$_POST['categoria_id']??0;
        $st=$this->db->prepare("SELECT nombre FROM categorias WHERE id=?");
        $st->execute([$id]);
        echo $st->fetchColumn()?:'';
        exit;
    }

    public function getEquiposPorCategoria()
    {
        $categoria_id = $_POST['categoria_id'] ?? 0;
        $equipo_local_id = $_POST['equipo_local_id'] ?? 0;

        $temporada = $this->getTemporadaActiva();
        if (!$temporada) exit('<option value="">No hay temporada activa</option>');

        $stmt = $this->db->prepare("
        SELECT e.id, e.nombre, c.nombre AS categoria_nombre
        FROM equipos e
        LEFT JOIN categorias c ON e.categoria_id = c.id
        WHERE e.temporada_id = :t
          AND e.categoria_id = :c
          AND e.id != :l
        ORDER BY e.nombre ASC
    ");
        $stmt->execute([
            ':t' => $temporada['id'],
            ':c' => $categoria_id,
            ':l' => $equipo_local_id
        ]);
        $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $html = '<option value="">-- Selecciona equipo visitante --</option>';
        foreach ($equipos as $eq) {
            // Abreviatura
            $cat = strtoupper(trim($eq['categoria_nombre'] ?? ''));
            $abbr = '';
            foreach (explode(' ', $cat) as $w) {
                if (mb_strlen($w) > 2) $abbr .= mb_substr($w, 0, 1);
            }
            $html .= '<option value="' . htmlspecialchars($eq['id']) . '">' . htmlspecialchars($eq['nombre'] . " ($abbr)") . '</option>';
        }

        echo $html;
        exit;
    }


    public function obtenerTarifa()
    {
        $c=$_POST['categoria_id']??0;
        $r=$_POST['rol']??'';
        $t=$_POST['temporada_id']??0;
        $st=$this->db->prepare("SELECT importe FROM tarifas WHERE categoria_id=:c AND temporada_id=:t AND rol=:r LIMIT 1");
        $st->execute([':c'=>$c,':t'=>$t,':r'=>$r]);
        echo json_encode(['importe'=>(float)($st->fetchColumn()?:0)]);
        exit;
    }

    // ==========================================================
    // 🗑️ ELIMINAR
    // ==========================================================
    public function eliminar($id)
    {
        SessionGuard::check();
        $st=$this->db->prepare("DELETE FROM partidos WHERE id=:id");
        $st->execute([':id'=>$id]);
        $_SESSION['flash_success']="✅ Partido eliminado correctamente.";
        header("Location: ".BASE_URL."partidos");
        exit;
    }

}
