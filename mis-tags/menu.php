<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Administración</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="usuarios.php">Usuarios</a></li>
                <li class="nav-item"><a class="nav-link" href="productos.php">Productos</a></li>
                <li class="nav-item"><a class="nav-link" href="pedidos.php">Pedidos</a></li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <form method="POST" action="logout.php" class="d-flex">
                        <button type="submit" class="btn btn-outline-light">
                            Cerrar Sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>