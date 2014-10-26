<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Actors Connections">
    <meta name="author" content="Piotr Gębala">

    <title>Actors Connections</title>
    <link href="<?php echo base_url('/public/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('/public/css/bootstrap-dialog.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('/public/css/custom.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('/public/css/font-awesome-4.1.0/css/font-awesome.min.css'); ?>" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">
    <!--[if lt IE 9]>
        <script src="<?php echo base_url('public/js/html5shiv.js'); ?>"></script>
        <script src="<?php echo base_url('public/js/respond.min.js'); ?>"></script>
    <![endif]-->
    <script src="<?php echo base_url('public/js/jquery-1.11.0.js'); ?>"></script>
    <script src="<?php echo base_url('public/js/jquery-ui.min.js'); ?>"></script>
    <script src="<?php echo base_url('public/js/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo base_url('public/js/bootstrap-dialog.js'); ?>"></script>
</head>
<body>
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <a id="logo" class="navbar-brand" href="#">
                    <span class="glyphicon glyphicon-user">
                    </span>
                    <span class="glyphicon glyphicon-film">
                    </span>
                    <span class="glyphicon glyphicon-user">
                    </span>
                    actors-connections.net
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a id="m_main" href="#">Main</a>
                    </li>
                    <li>
                        <a id="m_about" href="#">About</a>
                    </li>
                    <li>
                        <a id="m_stats" href="#">Statistics</a>
                    </li>
                    <li>
                        <a id="m_contact" href="#">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>





    <div class="intro-header" id="main">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="intro-message">
                        <h1>Choose Actors</h1>
                        <div class="input-group">
                            <span class="glyphicon glyphicon-user input-group-addon"> Actor #1 </span>
                            <input type="text" class="form-control" placeholder="Enter Name" id="actor1">
                        </div>
                        <div class="separator" style="margin:40px;">
                        </div>
                        <div class="input-group">
                          <span class="glyphicon glyphicon-user input-group-addon"> Actor #2 </span>
                          <input type="text" class="form-control" placeholder="Enter Name" id="actor2">
                        </div>
                        <h2> and...</h2>
                        <button id="Search" class="btn btn-primary">PRESS HERE</button>
                    </div>
                </div>
            </div>
        </div>
    </div>





    <div class="content-section-b" id="about">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-sm-4">
                    <hr class="section-heading-spacer">
                    <div class="clearfix"></div>
                    <h1>About:</h1>
                    <div class="clearfix"></div>
                    <img class="img-responsive" src="public/imgs/sixdegsep.jpg" alt="">
                </div>
                <div class="col-lg-8 col-sm-8">
                    <p class="lead">
                        In 1929 <b>Frigyes Karinthy</b> wrote: 
                    </p> 
                    <blockquote>
                        Planet Earth has never been as tiny as it is now.
                        It shrunk - relatively speaking of course - due to the quickening pulse of both physical and verbal communication. <a id="link_1" href="#">[1]</a>
                    </blockquote>
                    <p class="lead">
                        6 degrees of separation is a theory formed by people like Karinthly that says:
                    </p>
                    <blockquote>
                    Everyone and everything is six or fewer steps away, by way of introduction, from any other person in the world, so that a chain of "a friend of a friend" statements can be made to connect any two people in a maximum of six steps. <a id="link_2" href="#">[2]</a>
                    </blockquote>
                    <p class="lead">
                        This WebSite is a Proof-Of-Concept to this theory. Database contains over 2 000 000 actors and actresses. Conections among them are made based on movies they played.
                    </p>
                </div>
            </div>
        </div>
    </div>





    <div class="content-section-a" id="stats">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-sm-4">
                    <hr class="section-heading-spacer">
                    <div class="clearfix"></div>
                    <h2 class="section-heading">Some Numbers:</h2>
                        <p class="lead"><b>
                            <?php echo number_format ($stats['actors'], 0, ' ', ' '); ?></b> Actors &amp; Actresses;
                        </p>
                        <p class="lead"><b>
                            <?php echo number_format ($stats['movies'], 0, ' ', ' '); ?></b> Movies &amp; TV Shows;
                        </p>
                        <p class="lead"><b>
                            <?php echo number_format ($stats['links'], 0, ' ', ' '); ?></b> Links;
                        </p>
                    <h2 class="section-heading">Some Explenation:</h2>
                        <p class="lead">
                            <span class="stats stats_queries"></span>
                            Total Queries
                        </p>
                        <p class="lead">
                            <span class="stats stats_cached"></span>
                            Previously Cached
                        </p>
                        <p class="lead">
                            <span class="stats stats_found"></span>
                            Searched And Found
                        </p>
                </div>
                <div class="col-lg-8 col-sm-8">
                    <img class="img-responsive" src="ajax/statistics" alt="">
                </div>
            </div>

        </div>
    </div>





    <div class="content-section-b" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-sm-4">
                    <hr class="section-heading-spacer">
                    <div class="clearfix"></div>
                    <h2 class="section-heading">Contact:</h2>
                    <img class="img-responsive" src="public/imgs/contact.png" alt="">

                </div>
                <div class="col-lg-8 col-sm-8">
                    <br />
                    <div class="input-group">
                        <span class="input-group-addon">Name:</span>
                        <input type="text" class="form-control" placeholder="Enter Name" id="name">
                    </div>
                    <br />
                    <div class="input-group">
                        <span class="input-group-addon">Email:</span>
                        <input type="text" class="form-control" placeholder="Enter Email Address" id="email">
                    </div>
                    <br />
                    <div class="input-group">
                        <span class="input-group-addon">Message:</span>
                        <textarea class="form-control" id="message" rows="7"></textarea>
                    </div>
                    <br />
                    <div class="input-group" id="captcha-img">
                    <?php echo $captcha; ?>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">Captcha:</span>
                        <input type="text" class="form-control" placeholder="Enter above code" id="captcha">
                    </div>

                    <br />
                    <button id="submit" class="btn btn-primary">Send</button>
                </div>
            </div>
        </div>
    </div>





    <footer>
        <div class="container" id="footer">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="list-inline">
                        <li>
                            <a id="f_main" href="#">Main</a>
                        </li>
                        <li class="footer-menu-divider">&sdot;</li>
                        <li>
                            <a id="f_about" href="#">About</a>
                        </li>
                        <li class="footer-menu-divider">&sdot;</li>
                        <li>
                            <a id="f_stats" href="#">Statistics</a>
                        </li>
                        <li class="footer-menu-divider">&sdot;</li>
                        <li>
                            <a id="f_contact" href="#">Contact</a>
                        </li>
                    </ul>
                    <p class="copyright text-muted">
                    Scripts: Copyright &copy; Piotr 'Xex' Gębala <br />
                    Database: Copyright &copy; <a href="http://imdb.com" target="_blank">The Internet Movie Database</a> . All Rights Reserved <br />
                    Theme: <a href="http://startbootstrap.com/template-overviews/landing-page/" target="_blank">StarBootstrap: Landing Page</a><br/>
                    Contact logo: By adikhebat CC-BY-SA-3.0 (<a href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank">http://creativecommons.org/licenses/by-sa/3.0/</a>), via Wikimedia Commons<br />
                    [1] <a href="https://djjr-courses.wdfiles.com/local--files/soc180%3Akarinthy-chain-links/Karinthy-Chain-Links_1929.pdf" target="_blank">Frigyes Karinthy: Chain-Links (Translated from Hungarian and annotated by Adam Makkai and Enikö Jankó)</a><br />
                    [2] <a href="http://en.wikipedia.org/wiki/Six_degrees_of_separation" target="_blank"> Wikipedia: Six Degrees Of Separation</a><br />
                    </p>
                </div>
            </div>
        </div>
    </footer>
    <script src="<?php echo base_url('public/js/custom.js'); ?>"></script>
</body>
</html>