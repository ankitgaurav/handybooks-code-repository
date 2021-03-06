<?php
include ('files/config.php');
if(isset($_SESSION["user_id"])){
    header('Location:feeds');
}
else{
    //processing login form
if(isset($_POST["login_email"])){
//checking if email and password field are empty
    if($_POST["login_email"]!="" and $_POST["login_password"]!=""){
            $email=test_input($_POST["login_email"]);
            $password=test_input($_POST["login_password"]);
//querying the email from database
            $query = "SELECT * FROM users WHERE `email`='$email'" ;
            $result=mysqli_query($conn,$query);
            if(mysqli_num_rows($result)){
                $row=mysqli_fetch_assoc($result);
                $pass = $row["hash"];
                if(password_verify($password,$pass)){
                    $form = false;
                    //Setting session variables
                    $one=1;
                    $user_id=$row['user_id'];
                    $sql190 = "SELECT * FROM chats WHERE (to_user='$user_id' and displayble='$one' and to_user_read=0) ";
                    $result190 = mysqli_query($conn,$sql190) or mysqli_error($conn);
                    $_SESSION['no_of_unread_msg']= mysqli_num_rows($result190);
                    $_SESSION["user_id"] = $row['user_id'];
                    $_SESSION["email"] = $row['email'];
                    $_SESSION["name"] = $row['name'];
                    
                    header("Location:home");
                }
                else{
                    
                    $msg = "Wrong password";
                    header("Location:login.php?from=feeds");
                }            
        }
        else{
            $msg = "Email id not yet registered!";
            header("Location:login.php?from=feeds");
        }
    }
    else{
        $msg = "Please fill both email and password fields!";
        header("Location:login.php?from=feeds");
    }
}
    //login form process ends
    //checking if signup_form has been sent
    elseif(isset($_POST["signup_name"]) && $_POST['signup_email']!="" && $_POST["signup_password"]){
        //checking if fields are empty
        if($_POST["signup_name"]!="" or $_POST["email"]!="" or $_POST["password"]!=""){
            //sanitizing and validating form inputs
            $name = test_input($_POST["signup_name"]);
            $email = test_input($_POST["signup_email"]);
            $password =test_input($_POST["signup_password"]);

            //hashing passwords
            $password = password_hash($password, PASSWORD_DEFAULT);
            //checking if email exists
            $result = $conn->query('SELECT user_id FROM users WHERE email ="'.$email.'"');        
            $count = $result->num_rows;
            if($count==0){
                //data insert here
                $sql = "INSERT INTO users (name, email, hash) VALUES ('$name', '$email', '$password')";

                if($conn->query($sql)=== TRUE){
                    $form = false;

                    //Setting session variables
                    $sql2 = "SELECT * FROM users WHERE `email`='$email'";
                    $row = mysqli_query($conn,$sql2);
                    $user_data = mysqli_fetch_assoc($row);

                    $_SESSION["user_id"] = $user_data['user_id'];
                    $_SESSION["email"] = $user_data['email'];
                    $_SESSION["name"] = $user_data['name'];
                    $_SESSION["first_login"] = true;

                    //sending signup email to user
                    include ('signup_mail.php');
                    header('Location:dashboard?msg=greetings');
                }
                else 
                {
                    $form = true;
                    $msg = "Something went wrong. Data could not be inserted properly" . $conn->error;
                }
            }
            else{
                $form = true;
                $name = $name;
                $msg="Email id already taken";
            }
            
        }
    }
            else{
                $form = true;
            }
}
if($form == true){
?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <title>Handybooks.in - Best place to buy, sell, donate or exchange used/ second-hand books</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="description"   content="Buy, Sell, Donate and Exchange second hand easily in Handybooks.in. You can sell your old books or buy a second hand book easily with handybooks.in " />
    <meta property="og:url"           content="http://www.handybooks.in/" />
    <meta property="og:type"          content="website" />
    <meta property="og:title"         content="The best place to exchange second hand books" />
    <meta property="og:description"   content="Buy, Sell, Donate and Exchange second hand easily in Handybooks.in. You can sell your old books or buy a second hand book easily with handybooks.in " />
    <meta property="og:image"         content="http://handybooks.in/images/logo.png" />
    <link rel="stylesheet" href="bootstrap.min.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="styles/default_lp.css">
    <link rel="stylesheet" type="text/css" href="footer.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    
    <style type="text/css">
        .post_icons{
            color:#888888;
        }
    	.cta_btns{
    		min-width:250px;
    	}
        .tagline{
            font-family: 'Open Sans', sans-serif;
            }
        #mast_head{
            background: url(images/mast_head1.jpg) no-repeat;
            background-size: cover;

        }
        #hints{
            background-color: white;
            border: 1px #CCCCCC solid;
            border-radius: 5px;
            position: absolute;
        }
        a .txt_decor_no hover{
            text-decoration: none;
        }
        .browse-buttons{
            margin: 3px;
            border-color: #A9D8B2;
        }
        .browse-buttons:hover{
            background-color: #E9F2EB;
        }
        .browse-buttons:active{
            background-color: #E9F2EB !important;
        }
        .hh{
            background-color: #F8F8F8;
            border-radius: 5px 5px 0px 0px;
            border-top:1px solid #337AB7;
            border-right:1px solid #337AB7 ;
            border-bottom: 1px solid #337AB7;
                        

        }
        .hh:first-child{
            border-left: 1px solid #337AB7;
        }
        .nav-tabs {
    border-bottom: 1px solid #337AB7;
}
        #posts_lists .hh li a
        {
        font-weight: bold!important;
        border-right: 0px;
        border-top: 0px;
        }
        .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover{
            border-color: transparent; 
        }
    </style>
    <script>
function on_m_dn(){
document.getElementById("password").type = "text";
}
function on_m_up(){
document.getElementById("password").type = "password";
}

        function save_user_info(){
                var name = edit_form.name.value;
                var institution = edit_form.institution.value;                
                var city = edit_form.city.value;
                var feeds = "feeds";
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200 ){
                        document.getElementById("modal-body").innerHTML = xmlhttp.responseText;
                    }
                }
                xmlhttp.open('POST', 'process_save_user_info.php?name='+name+'&institution='+institution+'&city='+city+'&from='+feeds, true);
                xmlhttp.send();
            }

            function showLatestPosts(){
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200 ){
                        document.getElementById("latest_posts_div").innerHTML = xmlhttp.responseText;
                    }
                }
                xmlhttp.open('POST', 'process_show_latest_posts.php', true);
                xmlhttp.send();
            }
    </script>
    </head>
    <body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=410824605770189";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
       <nav class="navbar navbar-default" style="margin-bottom:0;   ">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                    <a href="home"class="logo_text">
                        <strong>handybooks</strong>
                        <small>.in</small>
                    </a>
            </div>
            
            <form class="navbar-form navbar-left" role="search" action="search_books" method="GET">
                <div class="input-group">
                    <input name="search_keyword" type="text" class="form-control" placeholder="Search books, author etc" >
                    <span class="input-group-btn">
                    <button class="btn btn-default" type="submit">
                    <i class="fa fa-search"></i>
                    </button>
                    </span>
                </div>
            </form>
            <a href="post_a_book" class="btn btn-success navbar-btn navbar-left" style="margin-right:5px;" id="post_ad_btn">POST AN AD</a>
        <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false" aria-has-popup="true" role="button"><i class="fa fa-user"></i> Sign In<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header">Existing User</li>
                <li><a href="login">Log In</a></li>
                <li class="divider"></li>
                <li class="dropdown-header">New User</li>
                <li><a href="signup">Register</a></li>
              </ul>
            </ul>
            </li>
        </div>
        </div>
        </nav>
        <br>

        <div class="container">
            <div class="hero_header alert alert-default" style="border:solid 1px #E8E8E8;background-color:#f9f7f9;"  >
                <!-- <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> -->
                <div class="row">
                    <div class="col-xs-12 col-sm-3">
                        <span class="tagline" style="font-size:16px;"><strong>Handybooks.in is a the best place for students and book-lovers to buy, sell and exchange their used books with ease.</strong></span><br><br>
                        <a href="signup" role="button" class="btn btn-danger">Sign up</a>
                    </div>
                    <div class="col-xs-4 col-sm-3" style="text-align:center;color:#b3b1b3;">
                        <i class="fa fa-4x fa-book"></i><br><br>
                        <h4>Buy/sell books, notes, magazines or navels
                    </div>
                    <div class="col-xs-4 col-sm-3" style="text-align:center;color:#b3b1b3;">
                        <i class="fa fa-4x fa-comments "></i><br><br>
                        <h4>Email or chat without losing privacy</h4>
                    </div>
                    <div class="col-xs-4 col-sm-3" style="text-align:center;color:#b3b1b3;">
                            <i class="fa fa-4x fa-institution"></i><br><br>
                            <h4>Search within your college campus or in the locality</h4>
                    </div>
                </div>
            </div>
        </div>
        
    <!-- Masthead Container -->
    <!-- <div class="row" id="mast_head" style="margin-left:0;margin-right:0;">
        <div class="container" style="padding-top:10px; padding-bottom:10px;">
            <div class="col-md-9">
            <div class="jumbotron" style="background-color:transparent;">
                <h1 class="tagline" style="color:#333333;text-shadow: 2px 2px 8px white;font-weight:500;">Exchanging used books was never so easy.</h1>
            </div>
            </div>
            <div class="col-md-3" style="padding-top:50px;">
            <a role="button" class="btn btn-danger btn-lg cta_btns" href="post_a_book"><strong>Sell Your Used Books&nbsp; </strong><i class="fa fa-book"></i></a><br><br>
            <a role="button" class="btn btn-primary btn-lg cta_btns" href="#content"><strong>Browse Books to Buy &nbsp;</strong><i class="fa fa-search"></i></a><br><br>
            <a role="button" class="btn btn-primary btn-lg cta_btns" href="book_request"><strong>Request for a Book &nbsp;</strong><i class="fa fa-shopping-cart"></i></a><br>
            </div><br>
            </div>
            </div> -->
                        <!-- Main Content div starts here -->
<div class="container">
                         <div class="row">
            <div class="col-md-9" id="posts_lists"><br>
                <ul class="nav nav-tabs">
                    <li role="presentation" class="active hh">
                        <a href="#latest_posts_div" onclick="showLatestPosts()">Latest Posts</a>
                    </li>
                    <li role="presentation" class="hh">
                        <a href="#">Books on Sale</a>
                    </li>
                    <li role="presentation" class="hh">
                        <a href="#">Books in Demand</a>
                    </li>
                </ul></h4><br>
                    <div id="latest_posts_div">         
                    <?php
                    //for pagination and displaying latest posts
                        $query = "SELECT * FROM post WHERE user_id<> '0' and deleted=false";
                        $total_pages = mysqli_num_rows(mysqli_query($conn,$query));
                        $targetpage = "home";
                        $limit = 7;
                        if(isset($_GET['page'])){
                            $page = $_GET['page'];
                            $start = ($page - 1) * $limit;
                        }   
                        else{
                            $page = 0;
                            $start = 0;
                        }

                        $sql = "SELECT * FROM post WHERE user_id<> '0' and deleted=false ORDER BY post_time DESC LIMIT $start, $limit";
                        $row = mysqli_query($conn,$sql);
                        while($posts = mysqli_fetch_assoc($row)){
                            $link='book_details?p_id='.$posts["post_id"];
                             if($posts["post_type"]=="donate"){
                                $price = "Free: On Donate";
                            }
                            elseif ($posts["post_type"]=="exchange") {
                                $price = "On Exchange";
                            }
                            else{
                                $price = "On sale for &nbsp;<i class='fa fa-inr'></i>' ".$posts["post_price"];
                            }

                            $time = $posts["post_time"];
                            $time = strtotime($time);
                            $time = " ".date("jS M", $time);

                            echo "<div style='display:block;' href='$link' class='latest_posts alert alert-default'>";
                            if ($posts["image_path"] != ""){
                                echo '<div class="row"><div class="col-md-2"><img src="' . $posts["image_path"] . '" width="100px" height="100px"></div>';
                            }
                              else{
                                    echo '<div class="row"><div class="col-md-2"><i class="fa fa-book fa-5x"></i></div>';
                                }
                            // if ($posts['post_category']=="Academic" or $posts['post_category']=="uncategorized") {
                            //     echo '<div class="row"><div class="col-md-2"><i class="fa fa-5x fa-leanpub" style="color:#777777"></i></div>';
                            // } 
                            // elseif($posts['post_category']=="Novel"){
                            //     echo '<div class="row"><div class="col-md-2"><i class="fa fa-5x fa-book" style="color:#7C6F62;"></i></div>';
                            // }
                            // else{
                            //     echo '<div class="row"><div class="col-md-2"><i class="fa fa-5x fa-newspaper-o"></i></div>';
                            // }
                            if($posts['post_author']!=""){
                                $author = "by ".$posts["post_author"];
                            }
                            else{
                                $author = "";
                            }
                            $poster_id = $posts["user_id"];
                            if($poster_id==1){
                                $hb = 1;
                                echo '<div class="col-md-10"><h4 style="text-transform:capitalize;"><a href='.$link.'>'.$posts['post_name']." ".$author.'</a> <a href="#" data-toggle="tooltip" title="Handybooks Verified Seller" data-placement="top"><span class="text-primary fa-stack" style="font-size:10px;"><i class="fa fa-certificate fa-stack-2x"></i><i class="fa fa-check fa-stack-1x fa-inverse "></i></span></a><a href="'.$link.'"role="button" class="pull-right btn btn-xs btn-default"style="font-size: 14px;">See more</a></h4>';
                                echo '<p><small ><i class="fa fa-user post_icons"></i> Sold by: <span class="text-success">Handybooks at 50% of MRP</span> &nbsp;<span class="pull-right"><i class="fa fa-clock-o post_icons"></i> '.$time.'</span><br></small></p>';
                            }
                            else{
                                $hb = 0;
                                $sql_user = "SELECT * FROM users WHERE user_id='$poster_id'";
                                $row_user = mysqli_query($conn,$sql_user);
                                $extract_user=mysqli_fetch_assoc($row_user);
                                $user_institution = $extract_user["institution"];
                                $user_locality = $extract_user["city"];
                                $user_name = $extract_user["name"];
                                if($user_name!=""){
                                    $user_name = '<i class="fa fa-user post_icons"></i> Posted by: '.$user_name;
                                }
                                if($user_institution!=""){
                                    $institution= '&nbsp;&nbsp;&nbsp;<i class="fa fa-institution post_icons"></i> '.$user_institution;
                                }
                                else{
                                    $institution= "";
                                }
                                if($user_locality!=""){
                                    $user_locality= '&nbsp;&nbsp;<i class="fa fa-map-marker post_icons"></i> '.$user_locality;
                                }
                                else{
                                    $user_locality= "";
                                }
                                echo '<div class="col-md-10"><h4 style="text-transform:capitalize;"><a href='.$link.'>'.$posts['post_name']." ".$author.'</a><a href="'.$link.'"role="button" class="pull-right btn btn-xs btn-default"style="font-size: 14px;">See more</a></h4>';
                                echo '<p><small ><span class="text-success">' . $price . '</span> &nbsp;<span class="pull-right"><i class="fa fa-clock-o post_icons"></i> '.$time.'</span><br>'.$user_name.' '.$institution.' '.$user_locality.'</small></p>';
                            }
                            

                                //block for creating the tags string starts here
                                $tags = "";
                                if($posts['post_genre']!=""){
                                    $tags = $posts['post_genre'].", ";
                                }
                                if ($posts["post_subject"]!="") {
                                    $tags = $tags.$posts["post_subject"].", ";
                                }
                                if ($posts["post_department"]!="") {
                                    $tags = $tags.$posts["post_department"].", ";
                                }
                                if($posts['post_category']!=""){
                                    $tags = $tags.$posts["post_category"].", ";
                                }
                                if ($posts['post_class']!="") {
                                    $tags = $tags.$posts["post_class"].", ";
                                }
                                if($posts['post_subject']!=""){
                                    $tags = $tags.$posts["post_subject"].", ";
                                }
                                if($posts['post_year']!=""){
                                    $tags = $tags.$posts["post_year"].", ";
                                }
                                if($tags!=""){
                                    $tags = "<i class='fa fa-tags post_icons'></i> ".$tags;
                                }
                                //tags string creation ends here
                                echo $tags;
                                if($posts["post_description"]!=""){
                                    echo "<p>Description: ".$posts['post_description']."</p>";
                                }
                                echo "</div></div></div>";
                            }
                            echo '<ul class="pager">';
                            if ($page == 0) $page = 1;
                            $prev = $page - 1;
                            $next = $page + 1; 
                            $lastpage = ceil($total_pages/$limit);
                            $lpm1 = $lastpage - 1;
                            $pagination = "";
                            if($lastpage > 1)
                            { 
                                if($page>1){
                                    echo '<li class="previous"><a href="'.$targetpage.'?page='.$prev.'">Newer</a></li>';
                                }
                                else{
                                    echo '<li class="previous"><a role="button" class="btn btn-default "href="'.$targetpage.'?page='.$prev.'" disabled="disabled">Newer</a></li>';
                                }
                                if ($page < $lastpage){
                                    echo '<li class="next"><a href="'.$targetpage.'?page='.$next.'">Older</a></li>';
                                }
                                else{
                                    echo '<li class="next"><a href="'.$targetpage.'?page='.$next.'" role="button" class="btn btn-default"disabled="disabled">Older</a></li>';
                                }
                            }
                            echo '</ul>';
                    ?>
                    </div>
                    <!--latest_posts_div ends-->
                    <div id="latest_requests_div">
                        
                    </div>
            </div>

<!--                 right content ends here-->            
            </div>               
</div>
 
            </div>
            <!-- Content div ends here--> 
        </div></div></div>
<?php
include ('footer.php');
?>
<script src="jquery-1.11.2.min.js"></script>
<script src="bootstrap.min.js"></script>
<script src="js/show_hints.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
      var val,val1;
      function showLatestPosts(){
        document.getElementById("latest_posts_div").innerHTML = '<center><i class="fa fa-3x fa-spinner fa-pulse"></i></center>';
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    $('html,body').animate({
                        scrollTop: $("#posts_lists").offset().top},
                        'slow');
                document.getElementById("latest_posts_div").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "loadfeeds.php", true);
        xmlhttp.send();
      }
      function loadfeeds(val)
      {
        $('.browse-buttons').on("click",function(){  
        $('.browse-buttons').not(this).removeClass('active');
        $(this).toggleClass('active');
        });
        document.getElementById("latest_posts_div").innerHTML = '<center><i class="fa fa-3x fa-spinner fa-pulse"></i></center>';
        val1=val.value;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    $('html,body').animate({
                        scrollTop: $("#posts_lists").offset().top},
                        'slow');
                document.getElementById("latest_posts_div").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "loadfeeds.php?category="+val1, true);
        xmlhttp.send();
    }
var $checkboxes = $("input:checkbox");
$checkboxes.on("change", function(){
var opts = getFilters();
updatePosts(opts);
});

function getFilters(){
var opts = [];
$checkboxes.each(function(){
if(this.checked){
opts.push(this.value);
}
});
return opts;
}

function updatePosts(opts){
document.getElementById("latest_posts_div").innerHTML = '<center><i class="fa fa-3x fa-spinner fa-pulse"></i></center>';
var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    $('html,body').animate({
                        scrollTop: $("#posts_lists").offset().top},
                        'slow');
                document.getElementById("latest_posts_div").innerHTML = xmlhttp.responseText;
            }
        }
        var length=opts.length;
        xmlhttp.open("GET", "updatePosts.php?filter="+opts, true);
        xmlhttp.send();
}

</script>     

    </body>
</html>

<?php
}
?>