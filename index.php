<?php
session_start();

// ==========================================
// CONEXIÓN A LA BASE DE DATOS
// ==========================================
$conexion = new mysqli("localhost", "root", "", "cafeteria505");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");

// ==========================================
// GUARDAR CONTACTO
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["guardar"])) {

    $nombre = trim($_POST["nombre"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $mensaje = trim($_POST["mensaje"] ?? "");

    if ($nombre == "" || $correo == "" || $mensaje == "") {
        $_SESSION['mensaje_estado'] = "Todos los campos son obligatorios.";
        $_SESSION['tipo_mensaje'] = "error";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['mensaje_estado'] = "El correo no es válido.";
        $_SESSION['tipo_mensaje'] = "error";
    } else {
        $sql = "INSERT INTO cliente (nombre, correo, mensaje) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sss", $nombre, $correo, $mensaje);

            if ($stmt->execute()) {
                $_SESSION['mensaje_estado'] = "¡Gracias por contactarnos! Tu mensaje fue guardado correctamente. 🎀";
                $_SESSION['tipo_mensaje'] = "exito";
            } else {
                $_SESSION['mensaje_estado'] = "Error al guardar: " . $stmt->error;
                $_SESSION['tipo_mensaje'] = "error";
            }

            $stmt->close();
        } else {
            $_SESSION['mensaje_estado'] = "Error SQL: " . $conexion->error;
            $_SESSION['tipo_mensaje'] = "error";
        }
    }
    
    header("Location: index.html#contacto");
    exit();
}

// ==========================================
// ELIMINAR CONTACTO
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar"])) {

    $correoEliminar = trim($_POST["correoEliminar"] ?? "");

    if (!filter_var($correoEliminar, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['mensaje_estado'] = "El correo no es válido.";
        $_SESSION['tipo_mensaje'] = "error";
    } else {
        $sql = "DELETE FROM cliente WHERE correo = ? LIMIT 1";
        $stmt = $conexion->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $correoEliminar);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $_SESSION['mensaje_estado'] = "¡Registro eliminado correctamente! 💗";
                    $_SESSION['tipo_mensaje'] = "exito";
                } else {
                    $_SESSION['mensaje_estado'] = "No se encontró el correo indicado.";
                    $_SESSION['tipo_mensaje'] = "error";
                }
            } else {
                $_SESSION['mensaje_estado'] = "Error al eliminar: " . $stmt->error;
                $_SESSION['tipo_mensaje'] = "error";
            }

            $stmt->close();
        } else {
            $_SESSION['mensaje_estado'] = "Error SQL: " . $conexion->error;
            $_SESSION['tipo_mensaje'] = "error";
        }
    }

    header("Location: index.html#eliminar");
    exit();
}

$conexion->close();
?>