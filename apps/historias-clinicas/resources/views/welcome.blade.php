<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Base de html y css para la creaciÃ³n de sitios pertenecientes a la AdministraciÃ³n PÃºblica Nacional de la RepÃºblica Argentina.">
    <meta name="author" content="Francisco Arioli">
    <link rel="shortcut icon" href="img/favicon.ico"> <!-- Nav and address bar color -->
    <meta name="theme-color" content="#0072b8">
    <meta name="msapplication-navbutton-color" content="#0072b8">
    <meta name="apple-mobile-web-app-status-bar-style" content="#0072b8">

    <title>Sistema de GestiÃ³n</title>

    <!-- Fonts -->
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Encode+Sans:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" media="all" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('theme/poncho/css/argentina.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/poncho/css/encode-fontface.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/poncho/css/documentacion.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/poncho/css/icono-arg.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/poncho/css/custom.css') }}">
    <style>
        .main-footer {
            background: #fff;
            padding-top: 0px !important;
        }

        .main-footer .row>div:first-child {
            padding-right: 30px
        }

        .main-footer ul {
            margin-bottom: 32px;
            padding: 0
        }

        .main-footer li {
            list-style: none
        }

        .main-footer li a {
            display: block;
            font-size: 16px;
            padding: 6px 0
        }

        .main-footer li a:focus,
        .main-footer li a:hover {
            text-decoration: underline
        }

        .main-footer h4 {
            color: #767676
        }

        .main-footer img.image-responsive {
            margin-bottom: 20px;
            max-width: 300px;
            display: block
        }

        .brand-footer {
            padding: 0;
            width: 100%
        }

        .brand-footer img {
            float: none;
            margin: 0 auto
        }

        body.sticky-footer {
            height: 100%
        }

        body.sticky-footer .main-footer {
            display: table-row;
            height: 1px
        }

        body.sticky-footer .main-footer>div {
            padding-bottom: 30px;
            padding-top: 60px
        }

        .footer-item {
            display: flex;
            margin-bottom: 8px
        }

        .footer-item img {
            max-width: 50px;
            height: 100%;
            display: inline-block;
            margin: auto 0
        }

        .footer-item p {
            font-size: 16px;
            display: inline-block;
            margin: auto 0 auto 12px
        }
        .btn {
    border: 3px solid rgba(0, 0, 0, 0);
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 0px;
    padding: 0px;
    text-transform: inherit;
    vertical-align: top;
    white-space: normal;
    word-break: initial;
    text-decoration: none;
    letter-spacing: inherit;
    border-radius: 30px;
    line-height: 1.42857143
}
header .btn {
    margin: 0px !important;
    padding: 5px 15px !important;
}
.btn-link {
    color: #242c4f;
    background-color: #fff;
    border: 3px solid #ddd !important;
    text-decoration: none !important;
}

.btn-link:hover,
.btn-link:focus,
.btn-link:active {
    text-decoration: none !important;
    background-color: #f2f2f2 !important;
    color: #242c4f
}
    </style>
</head>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<body>
    <header>
        <!--<nav class="navbar navbar-top navbar-default bg-primary border-bottom-amarillo" role="navigation">
            <div class="container">
                <div>
                    <div class="navbar-header"> <a class="navbar-brand" href="/" aria-label="Argentina.gob.ar Presidencia de la NaciÃ³n"> <img alt="DPVER" src="{{ asset('theme/poncho/img/logo-blanco.png') }}" height="50"> </a>
                        @if (Route::has('login'))
                        @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-link btn-login visible-xs"> Home</a>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-link btn-login visible-xs"> Ingresar</a>
                        @if (Route::has('register'))
                        <a href="{{ route('login') }}" class="btn btn-link btn-login visible-xs"> Ingresar</a>
                        @endif
                        @endauth
                        @endif


                    </div>
                    <div class="nav navbar-nav navbar-right hidden-xs">
                        <a href="#" onclick="$('.navbar.navbar-top').removeClass('state-search');" class="btn btn-mi-argentina visible-xs"> <i class="fa fa-times"></i> </a>


                        @if (Route::has('login'))
                        @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-link hidden-xs bg-white" target="_blank"> Home</a>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-link hidden-xs bg-white" target="_blank"> Ingresar</a>
                        @if (Route::has('register'))
                        <a href="{{ route('login') }}" class="btn btn-link hidden-xs bg-white" target="_blank"> Ingresar</a>
                        @endif
                        @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>-->
        <nav class="navbar navbar-top navbar-default home-navbar">
            <div class="nav-mop-sgo">
                <div class="navbar-header"><a href="/" class="navbar-brand"><img src="{{ asset('theme/poncho/img/logo-blanco.png') }}" alt="" class="navbar-image"></a></div>
            
                <div class="nav navbar-nav navbar-right hidden-xs">
                @if (Route::has('login'))
                        @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-link hidden-xs bg-white" target="_blank"> Panel</a>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-link hidden-xs bg-white" target="_blank"> Ingresar</a>
                        @if (Route::has('register'))
                        <a href="{{ route('login') }}" class="btn btn-link hidden-xs bg-white" target="_blank"> Ingresar</a>
                        @endif
                        @endauth
                        @endif  
            </div>
            </div>
        </nav>
    </header>

    <main role="main">
        <section class="jumbotron" style="background-image: url('{{ asset('theme/poncho/img/fondo.jpg') }}'); padding-top: 76px;">
            <div class="row m-x-auto">
                <div class="col-xs-8 col-md-8 text-left m-y-auto cuadro-home-general">
                    <div class="hero-container">
                        <h2>Sistema de GestiÃ³n de Obras</h2>
                        <p> En esta plataforma podÃ©s gestionar la obra pÃºblica desde su etapa de planificaciÃ³n hasta su finalizaciÃ³n. De esta forma, podÃ©s controlar todo su ciclo de vida y contar con informaciÃ³n necesaria para la toma de decisiones. </p>
                    </div>
                </div>
            </div>
        </section>
        <section class="home-dashboard ">
            <div style="padding-bottom: 20px;"></div>
            <div class="container home-container ">
                <div class="row align-items-center">
                    <div class="col-xs-12 col-sm-6 col-md-3"><a class="panel panel-default panel-heading-green rounded panel-icon shadow-sm" href="{{ route('login') }}">
                            <div class="panel-heading hidden-xs"><img src="{{ asset('theme/poncho/img/iHomeProyectos.png') }}" class="panel-img"></div>
                            <div class="panel-body text-center">
                                <h3 class="hidden-md">Gestor de Proyectos</h3>
                                <h4 class="hidden-xs hidden-sm hidden-lg">Gestor de Proyectos</h4>
                            </div>
                        </a></div>

                    <div class="col-xs-12 col-sm-6 col-md-3"><a class="panel panel-default panel-heading-light-green rounded panel-icon shadow-sm" href="{{ route('login') }}">
                            <div class="panel-heading hidden-xs"><img src="{{ asset('theme/poncho/img/iObras.png') }}" class="panel-img"></div>
                            <div class="panel-body text-center">
                                <h3 class="hidden-md">Gestor de Obras</h3>
                                <h4 class="hidden-xs hidden-sm hidden-lg">Gestor de Obras</h4>
                            </div>
                        </a></div>
                </div>
                <section class="home-dashboard justify-content-center">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-3 m-x-1">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 m-b-2 shadow-sm rounded dashboard-item-container"><a href="#">
                                        <div class="row dashboard-item">
                                            <div class="col-md-4 m-y-auto"><img src="https://ppo.obraspublicas.gob.ar/images/home/geomop.svg"></div>
                                            <div class="col-md-8 m-y-auto p-a-0">
                                                <p class="text-bold">GEONODE</p>
                                            </div>
                                        </div>
                                    </a></div>
                                <div class="col-xs-12 col-sm-12 col-md-12 m-b-2 shadow-sm rounded dashboard-item-container"><a href="#">
                                        <div class="row dashboard-item">
                                            <div class="col-md-4 m-y-auto"><img src="https://ppo.obraspublicas.gob.ar/images/home/mapainversiones.svg"></div>
                                            <div class="col-md-8 m-y-auto p-a-0">
                                                <p class="text-bold">Mapa </p>
                                            </div>
                                        </div>
                                    </a></div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-3 m-x-1">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 m-b-2 shadow-sm rounded dashboard-item-container"><a href="#">
                                        <div class="row dashboard-item">
                                            <div class="col-xs-12 col-sm-12 col-md-4 m-y-auto"><img src="https://ppo.obraspublicas.gob.ar/images/home/plan.svg"></div>
                                            <div class="col-xs-12 col-sm-12 col-md-8 m-y-auto p-a-0">
                                                <p class="text-bold">Expedientes</p>
                                            </div>
                                        </div>
                                    </a></div>
                                <div class="col-xs-12 col-sm-12 col-md-12 m-b-2 shadow-sm rounded dashboard-item-container"><a href="https://www.argentina.gob.ar/obras-publicas/secretaria-gestion/transformacion-digital">
                                        <div class="row dashboard-item">
                                            <div class="col-xs-12 col-sm-12 col-md-4 m-y-auto"><img src="https://ppo.obraspublicas.gob.ar/images/home/plan.svg"></div>
                                            <div class="col-xs-12 col-sm-12 col-md-8 m-y-auto p-a-0">
                                                <p class="text-bold">Manuales</p>
                                            </div>
                                        </div>
                                    </a></div>
                            </div>
                        </div>
                    </div>
                </section>


            </div>



        </section>


    </main>
    <footer class="main-footer">
        <div class="container-fluid  border-top-amarillo">
            <div class="row sub-footer">
                <div class="container ">
                    <div class="col-xs-12 col-md-2"><img src="{{ asset('theme/poncho/img/dpver-blanco.png') }}" class="primero-gente-img"></div>
                    <div class="col-xs-12 col-md-5 col-lg-5 col-md-offset-5 col-lg-offset-5 text-left">
                        <div class="footer-item"><img src="{{ asset('theme/poncho/img/mop.png') }}">
                            <p>DirecciÃ³n Provincial de Vialidad de Entre RÃ­os</p>
                        </div>
                        <div class="footer-item"><img src="{{ asset('theme/poncho/img/ubicacion.png') }}">
                            <p>Av. Francisco RamÃ­rez 2197, ParanÃ¡, Entre RÃ­os</p>
                        </div>
                        <div class="footer-item"><img src="{{ asset('theme/poncho/img/tel.png') }}">
                            <p>Tel. +54 (0343) 424-8900</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>
