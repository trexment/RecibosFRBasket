# 🏀 **Recibos Arbitrales – Federación Riojana de Baloncesto**

Aplicación web para la gestión de **recibos arbitrales**, temporadas, equipos, categorías y partidos,  
con generación automática de PDFs, importación de designaciones (CSV/PDF), control de usuarios e interfaz moderna tipo **AdminLTE**.

---

## ⚙️ **1️⃣ Requisitos del servidor**

| Requisito | Versión mínima | Comentario |
|------------|----------------|-------------|
| PHP | 8.1+ | Recomendado PHP 8.3 |
| MySQL | 5.7+ | Compatible con MariaDB |
| Extensiones PHP | `pdo_mysql`, `mbstring`, `gd`, `openssl` | Necesarias para conexión y PDF |
| Servidor web | Apache / Nginx | Con soporte `.htaccess` |
| Hosting | VPS / Plesk | Verificado sobre Plesk |

---

## 🗂️ **2️⃣ Estructura del proyecto**

```
recibos/
│
├── app/
│   ├── config/config.php           ← configuración general (BD + correo)
│   ├── controllers/                ← controladores MVC
│   ├── core/Database.php           ← conexión PDO
│   ├── helpers/                    ← funciones de sesión, PDF, etc.
│   ├── libraries/                  ← PHPMailer, TCPDF, Smalot/PdfParser
│   ├── pdf/recibo_pdf.php          ← plantilla de PDF
│   ├── setup/                      ← scripts iniciales (admin / mail)
│   └── views/                      ← vistas HTML / PHP (modulares)
│
├── public/
│   ├── assets/css/custom.css       ← estilos personalizados
│   ├── img/                        ← logos (Federación + silbato)
│   ├── index.php                   ← router principal (URLs amigables)
│   └── .htaccess                   ← redirección MVC
│
└── README.md                       ← este archivo
```

---

## 🧩 **3️⃣ Base de datos**

1️⃣ Crear base de datos:
```sql
CREATE DATABASE recibos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2️⃣ Importar `app/config/recibos.sql`

3️⃣ Activar una temporada:
```sql
INSERT INTO temporadas (nombre, activa) VALUES ('2025/2026', 1);
```

---

## 🔧 **4️⃣ Configuración**

Editar `/app/config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'recibos');
define('DB_USER', 'recibosFRB');
define('DB_PASS', 'Sign@turit26');
define('BASE_URL', 'https://recibos.frannunez.es/');
```

---

## 👤 **5️⃣ Usuario administrador**

Ejecutar desde navegador:
```
https://recibos.frannunez.es/app/setup/setup_admin.php
```

| Campo | Valor |
|--------|--------|
| Nombre | Administrador |
| Email | admin@federacionriojana.es |
| Contraseña | admin123 |
| Rol | admin |

> 🔒 **Eliminar el archivo** `setup_admin.php` después de usarlo.

---

## ✉️ **6️⃣ Correo (PHPMailer)**

Accede a:
```
https://recibos.frannunez.es/app/setup/setup_mail.php
```

Completa los datos SMTP y guarda la configuración.  
Se actualizará automáticamente el `config.php`.

> ⚠️ Elimina `setup_mail.php` una vez configurado.

---

## 🚪 **7️⃣ Acceso y módulos**

### 🔹 Login
```
https://recibos.frannunez.es/login
```

### 🔹 Módulos disponibles

| Módulo | Descripción |
|---------|-------------|
| **Dashboard** | Resumen visual con gráficos (Chart.js) |
| **Temporadas** | Crear, activar y gestionar temporadas |
| **Categorías** | Crear y editar categorías generales |
| **Equipos** | Asociados a categoría y temporada activa |
| **Partidos** | Crear manualmente o importar (CSV/PDF) |
| **Árbitros** | Listado general para administradores |
| **Recibos** | Generación PDF (partidos / desplazamientos) |
| **Usuarios / Perfil** | Datos personales y bancarios |
| **Admin / Tarifas** | Configuración por rol y categoría |

---

## 📦 **8️⃣ Importaciones automáticas**

### 🟩 CSV (designaciones oficiales)

- Botón “📂 Importar CSV” en el listado de partidos.
- Admite formato exportado desde la plataforma federativa.
- Previsualiza los datos antes de confirmar.
- Importa:
  - Fecha  
  - Jornada  
  - Categoría  
  - Equipo local / visitante  
  - Rol y uso de tablet
- Calcula automáticamente las tarifas según temporada y rol.

### 🟦 PDF (designaciones en PDF)

- Usa la librería `Smalot/PdfParser`.
- Detecta automáticamente:
  - Fecha, categoría, jornada, equipos  
  - Rol según número de colegiado  
  - Tablet (anotador = sí, cronometrador = no)
- Permite revisar y corregir antes de guardar.

---

## 💰 **9️⃣ Tarifas y cálculos automáticos**

- Las tarifas se vinculan a **temporada**, **categoría** y **rol**.  
- Roles disponibles:
  - Árbitro
  - Árbitro (solo)
  - Oficial
  - Oficial (solo)
- Si el partido usa **tablet**, se suma automáticamente **+1 €** al importe.  
- El campo de **desplazamiento (€)** se calcula desde los **km × precio/km** configurado.  
- Todo se almacena por usuario y temporada activa.

---

## 📱 **🔟 Indicadores visuales en tablas**

| Icono | Significado |
|--------|--------------|
| 🚗 (`fa-car`) | Partido con desplazamiento |
| ➖ (`fa-minus`) | Sin desplazamiento |
| 📱 (`fa-tablet-alt`) | Usa tablet (+1 €) |
| 🚫 (`fa-ban`) | No usa tablet |

---

## 🎨 **11️⃣ Personalización visual**

- Tema: AdminLTE + colores de la bandera riojana  
- Favicon: `/public/img/silbato.ico`  
- Logo FRB: `/public/img/Federacion_riojana_baloncesto.png`

---

## 🔒 **12️⃣ Seguridad**

- Eliminar `setup_admin.php` y `setup_mail.php` tras usarlos.  
- Bloquear acceso directo a `/app/` vía `.htaccess`.  
- Sesiones protegidas con `SessionGuard`.  
- Logout automático tras 10 minutos de inactividad.

---

## 🧾 **13️⃣ Errores comunes**

| Error | Causa | Solución |
|--------|--------|-----------|
| `403 Forbidden` | `.htaccess` mal ubicado | Moverlo dentro de `/public` |
| `TCPDF ERROR` | Falta logo o fuente | Subir logos requeridos |
| `Integrity constraint violation` | Restricción FK antigua | `ALTER TABLE ... DROP FOREIGN KEY ...;` |
| `Deprecated: fgetcsv()` | PHP ≥8.3 requiere `$escape` | Método actualizado correctamente |
| `Partido duplicado` | Fecha + equipos ya existen | Ver validación automática |
| `PDF vacío` | No se seleccionó retención o rango | Revisar formulario antes de generar |

---

## 🧰 **14️⃣ Funciones clave**

| Función | Ubicación |
|----------|------------|
| Crear temporadas | `/temporadas` |
| Activar temporada | En listado (botón “Activar”) |
| Crear / importar partidos | `/partidos` |
| Generar recibos PDF | `/recibos` |
| Filtrar partidos por fecha | En recibos |
| Editar perfil y cuenta | `/usuarios/perfil` |
| Cierre automático sesión | `helpers/session_guard.php` |

---

## 🏁 **15️⃣ Créditos**

**Desarrollado por:**  
**Francisco Javier Núñez Prieto** *(Nuñez and Son / DJ Javnx)*  

**Framework:** PHP MVC + PDO  
**Frontend:** Bootstrap + AdminLTE  
**PDF:** TCPDF  
**Email:** PHPMailer  
**Parsing PDF:** Smalot/PdfParser  
**Gráficos:** Chart.js  
**Compatibilidad:** Plesk / Apache / Nginx  
**Licencia:** Uso interno – Federación Riojana de Baloncesto  

---

## ⚡ **Instalación rápida**

```bash
# 1. Subir los archivos al servidor (public como raíz web)
# 2. Crear base de datos y usuario
# 3. Importar recibos.sql
# 4. Configurar config.php (DB + BASE_URL)
# 5. Ejecutar:
#    https://tu-dominio.com/app/setup/setup_admin.php
# 6. Ejecutar:
#    https://tu-dominio.com/app/setup/setup_mail.php
# 7. Iniciar sesión:
#    https://tu-dominio.com/login
```

---

📩 **Soporte técnico**  
**Email:** frannunez.dev@gmail.com  
**Despliegue y mantenimiento:** Nuñez and Son – Soluciones digitales y eventos

