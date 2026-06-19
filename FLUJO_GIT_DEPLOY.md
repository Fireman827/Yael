# Flujo Git & Deploy — FHB Restaurant
## XAMPP (laptop) → GitHub → IIS (servidor Windows Server 2019)

---

## PROBLEMA UTF8MB4 (MariaDB local vs MySQL 8.0 producción)

Cuando importas un backup de producción a tu MariaDB local (XAMPP),
la collation `utf8mb4_0900_ai_ci` no existe en MariaDB 10.4.
Hay que reemplazarla antes de importar.

### PowerShell — laptop (reemplazar collation en el backup)
```powershell
(Get-Content "C:\ruta\al\backup.sql" -Raw) `
    -replace 'utf8mb4_0900_ai_ci', 'utf8mb4_general_ci' |
    Set-Content "C:\ruta\al\backup_fixed.sql"
```
Luego importa `backup_fixed.sql` en phpMyAdmin o con:
```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root fhbpos < "C:\ruta\al\backup_fixed.sql"
```

---

## FLUJO NORMAL DE TRABAJO

### 1. LAPTOP — Git Bash (hacer cambios y subir)

```bash
# Ver qué archivos cambiaron
git status

# Agregar archivos específicos (NUNCA uses git add . sin revisar antes)
git add html/archivo1.php html/archivo2.php

# Crear el commit
git commit -m "Descripción breve del cambio"

# Subir a GitHub
git push
```

---

### 2. SERVIDOR — Git Bash (bajar los cambios)

```bash
# Bajar los cambios de GitHub
git pull
```

Si hay conflictos o el servidor tiene cambios locales no deseados:
```bash
# Descartar todo lo local y quedar idéntico a GitHub (CUIDADO: irreversible)
git fetch origin
git reset --hard origin/main
```

---

### 3. SERVIDOR — PowerShell (tareas de administración)

#### Variables de entorno para deploy webhook (solo se hace una vez)
```powershell
[System.Environment]::SetEnvironmentVariable("DEPLOY_SECRET", "TU_SECRET_AQUI", "Machine")
[System.Environment]::SetEnvironmentVariable("DEPLOY_REPO_PATH", "C:/inetpub/wwwroot/fhbrestaurant/web", "Machine")
iisreset
```

#### Copiar database.php al app de pedidos (después de cada git pull si no existe)
```powershell
Copy-Item "C:\inetpub\wwwroot\fhbrestaurant\web\html\application\config\database.php" `
          "C:\inetpub\wwwroot\fhbrestaurant\web\html\pedidos\application\config\database.php" -Force
```

#### Copiar vistas de error de CI3 al app pedidos (solo la primera vez)
```powershell
Copy-Item -Recurse -Force `
    "C:\inetpub\wwwroot\fhbrestaurant\web\html\application\views\errors" `
    "C:\inetpub\wwwroot\fhbrestaurant\web\html\pedidos\application\views\errors"
```

#### Instalar dependencias PHP (si se agregó algún paquete nuevo en composer.json)
```powershell
cd C:\inetpub\wwwroot\fhbrestaurant\web\html
composer install --no-dev
```

#### Reiniciar IIS (si algo no carga después del pull)
```powershell
iisreset
```

---

## TABLA DE BASE DE DATOS — SESIONES (solo se crea una vez)

Si aparece error "Table ci_sessions_pedidos doesn't exist", ejecutar en MySQL:

```sql
CREATE TABLE IF NOT EXISTS `ci_sessions_pedidos` (
    `id`         varchar(128) NOT NULL,
    `ip_address` varchar(45)  NOT NULL,
    `timestamp`  int(10) unsigned NOT NULL DEFAULT 0,
    `data`       blob         NOT NULL,
    PRIMARY KEY (`id`),
    KEY `ci_sessions_pedidos_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

---

## ARCHIVOS QUE NUNCA VAN AL REPO (están en .gitignore)

| Archivo | Por qué |
|---|---|
| `html/application/config/database.php` | Credenciales de BD |
| `html/pedidos/application/config/database.php` | Credenciales de BD |
| `html/application/logs/` | Logs del sistema |
| `html/pedidos/application/logs/` | Logs del sistema |
| `html/uploads/` | Archivos subidos por usuarios |
| `html/vendor/` | Paquetes Composer (se instalan con composer install) |
| `html/phpMyAdmin/` | Herramienta de administración |

---

## DEPLOY SECRET

El secreto del webhook NUNCA va en el código.
Está guardado como variable de entorno en el servidor (ver sección 3).
Valor actual guardado en: variable de entorno DEPLOY_SECRET del servidor.

---

## REFERENCIA RÁPIDA — ¿Dónde está cada cosa?

| Qué | Dónde |
|---|---|
| POS (sistema de caja) | `http://10.10.20.27/` |
| Pedidos online (clientes) | `http://10.10.20.27/pedidos/` |
| Panel admin online (dentro del POS) | `http://10.10.20.27/AdminOnline/ordenes` |
| Repo GitHub | https://github.com/Fireman827/Yael |
| BD local | `fhbpos` en MariaDB 10.4 (XAMPP) |
| BD producción | `fhbrestaurant` en MySQL 8.0 (servidor) |
