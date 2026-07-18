<?php
require_once __DIR__ . '/../helpers/vista.php';

class UsuarioController
{
    public function mostrarRegistro(): void
    {
        $this->render();
    }

    public function registrar(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->render('crear-cuenta');
            return;
        }

        $rol = trim($_POST['rol'] ?? '');
        $correo = strtolower(trim($_POST['correo'] ?? ''));
        $contrasena = (string) ($_POST['contrasena'] ?? '');
        $confirmarContrasena = (string) ($_POST['confirmar_contrasena'] ?? '');
        $nombreCompleto = trim($_POST['nombre_completo'] ?? '');
        $nombreOrganizacion = trim($_POST['nombre_organizacion'] ?? '');

        $datosRegistro = [
            'rol'                 => $rol,
            'correo'              => $correo,
            'nombre_completo'     => $nombreCompleto,
            'nombre_organizacion' => $nombreOrganizacion,
        ];

        $errores = $this->validarRegistro($rol, $correo, $contrasena, $confirmarContrasena, $nombreCompleto, $nombreOrganizacion);

        if (empty($errores)) {
            $usuarioModel = new UsuarioModel();
            if ($usuarioModel->buscarPorCorreo($correo) !== null) {
                $errores[] = 'Ese correo ya está registrado. Iniciá sesión o usá otro correo.';
            }
        }

        if (!empty($errores)) {
            $this->render('crear-cuenta', [], $errores, $datosRegistro);
            return;
        }

        $conexion = obtenerConexion();
        $conexion->beginTransaction();

        try {
            $usuarioModel = new UsuarioModel();
            $idUsuario = $usuarioModel->crearUsuario($correo, password_hash($contrasena, PASSWORD_DEFAULT), $rol);

            if ($rol === ROL_VOLUNTARIO) {
                (new VoluntarioModel())->crearVoluntario($idUsuario, $nombreCompleto);
            } else {
                (new OrganizacionModel())->crearOrganizacion($idUsuario, $nombreOrganizacion);
            }

            $conexion->commit();
        } catch (PDOException $excepcion) {
            $conexion->rollBack();
            error_log('Error al registrar usuario: ' . $excepcion->getMessage());
            $this->render('crear-cuenta', [], ['No se pudo completar el registro. Probá de nuevo en unos minutos.'], $datosRegistro);
            return;
        }

        $this->render('iniciar-sesion', [], [], [], $correo, 'Cuenta creada correctamente. Iniciá sesión para continuar.');
    }

    public function iniciarSesion(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->render();
            return;
        }

        $correo = strtolower(trim($_POST['correo'] ?? ''));
        $contrasena = (string) ($_POST['contrasena'] ?? '');

        $errores = [];

        if ($correo === '' || $contrasena === '') {
            $errores[] = 'Ingresá tu correo y tu contraseña.';
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo no tiene un formato válido.';
        }

        $usuario = null;
        if (empty($errores)) {
            $usuario = (new UsuarioModel())->verificarCredenciales($correo, $contrasena);
            if ($usuario === null) {
                $errores[] = 'Correo o contraseña incorrectos.';
            }
        }

        if (!empty($errores)) {
            $this->render('iniciar-sesion', $errores, [], [], $correo);
            return;
        }

        $idUsuario = (int) $usuario['id'];
        $rol = $usuario['rol'];

        $perfil = $rol === ROL_VOLUNTARIO
            ? (new VoluntarioModel())->buscarPorIdUsuario($idUsuario)
            : (new OrganizacionModel())->buscarPorIdUsuario($idUsuario);

        $this->crearSesion($idUsuario, $rol, (int) $perfil['id']);
        $this->redirigirSegunRol($rol);
    }

    public function cerrarSesion(): void
    {
        $_SESSION = [];
        session_destroy();
        header('Location: ' . BASE_URL);
        exit;
    }

    /**
     * Valida los datos del formulario de registro (RNF03) antes de tocar la base de datos.
     */
    private function validarRegistro(
        string $rol,
        string $correo,
        string $contrasena,
        string $confirmarContrasena,
        string $nombreCompleto,
        string $nombreOrganizacion
    ): array {
        $errores = [];

        if ($rol !== ROL_VOLUNTARIO && $rol !== ROL_ORGANIZACION) {
            $errores[] = 'Seleccioná un tipo de cuenta válido.';
        }

        if ($correo === '') {
            $errores[] = 'El correo es obligatorio.';
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 150) {
            $errores[] = 'El correo no tiene un formato válido.';
        }

        if ($contrasena === '') {
            $errores[] = 'La contraseña es obligatoria.';
        } elseif (strlen($contrasena) < 8) {
            $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
        } elseif ($contrasena !== $confirmarContrasena) {
            $errores[] = 'Las contraseñas no coinciden.';
        }

        if ($rol === ROL_VOLUNTARIO) {
            if ($nombreCompleto === '') {
                $errores[] = 'El nombre completo es obligatorio.';
            } elseif (strlen($nombreCompleto) > 150) {
                $errores[] = 'El nombre completo no puede superar los 150 caracteres.';
            }
        }

        if ($rol === ROL_ORGANIZACION) {
            if ($nombreOrganizacion === '') {
                $errores[] = 'El nombre de la organización es obligatorio.';
            } elseif (strlen($nombreOrganizacion) > 150) {
                $errores[] = 'El nombre de la organización no puede superar los 150 caracteres.';
            }
        }

        return $errores;
    }

    private function crearSesion(int $idUsuario, string $rol, int $idPerfil): void
    {
        $_SESSION['id_usuario'] = $idUsuario;
        $_SESSION['rol'] = $rol;

        if ($rol === ROL_VOLUNTARIO) {
            $_SESSION['id_voluntario'] = $idPerfil;
        } else {
            $_SESSION['id_organizacion'] = $idPerfil;
        }
    }

    private function redirigirSegunRol(string $rol): void
    {
        $destino = $rol === ROL_VOLUNTARIO ? 'ver_perfil_voluntario' : 'ver_perfil_organizacion';
        header('Location: ' . BASE_URL . '?accion=' . $destino);
        exit;
    }

    private function render(
        string $pestanaActiva = 'iniciar-sesion',
        array $erroresLogin = [],
        array $erroresRegistro = [],
        array $datosRegistro = [],
        string $correoLogin = '',
        string $mensajeExito = ''
    ): void {
        require __DIR__ . '/../views/registro.php';
    }
}
