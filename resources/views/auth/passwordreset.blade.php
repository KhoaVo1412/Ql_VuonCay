<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="/FontAwesome6.4Pro/css/all.css">

    {{-- <link rel="stylesheet" href="assets/css/style.css"> --}}
    <link rel="shortcut icon" href="./imgs/favicon.ico" type="image/x-icon">

</head>

<body>
  <div class="loading-wrapper">
        <span class="loader"></span>
    </div>

    <style>
        .loading-wrapper {
            position: fixed;
            background-color: rgb(248, 248, 248);
            z-index: 1000;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center
        }
        .loader {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: inline-block;
        border-top: 4px solid #FFF;
        border-right: 4px solid transparent;
        box-sizing: border-box;
        animation: rotation 1s linear infinite;
        }
        .loader::after {
        content: '';  
        box-sizing: border-box;
        position: fixed;
        left: 0;
        top: 0;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        border-bottom: 4px solid #750102;
        border-left: 4px solid transparent;
        }
        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        } 

    </style>
    <div class="login-dark">

      <form method="POST" id="form-reset">
            @csrf 
            {{-- <h2 class="sr-only">Login Form</h2> --}}
            <div class="illustration"><i class="icon ion-ios-locked-outline"></i></div>
            
            @csrf 
            <input type="hidden" name="token" value="">
            <input type="hidden" name="email" value="{{ request('email') }}">

            <h5 class="mb-3">Khôi phục mật khẩu</h5>

            <div class="form-group position-relative">
                <input 
                    class="form-control @error('password') is-invalid @enderror" 
                    type="password" 
                    name="password" 
                    placeholder="Mật khẩu mới"> 
                <i class="fa-solid fa-eye" onclick="togglePassword(this)" style="cursor: pointer; position: absolute;right: 10px;top: 50%;transform: translateY(-50%);"></i>
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group position-relative">
                <input 
                    class="form-control @error('password_confirmation') is-invalid @enderror" 
                    type="password" 
                    name="password_confirmation" 
                    placeholder="Xác nhận mật khẩu mới">
                <i class="fa-solid fa-eye" onclick="togglePassword(this)" style="cursor: pointer; position: absolute;right: 10px;top: 50%;transform: translateY(-50%);"></i>
                @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit">Xác nhận</button>
            </div>
            
        </form>
    
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>

    <script>
      $(document).ready(function() {

        $("#form-log").addClass("no-transition");
        $("#form-forgot").addClass("no-transition");

       
        setTimeout(function() {
            $("#form-log").removeClass("no-transition");
            $("#form-forgot").removeClass("no-transition");
        }, 10);
        
        $(".forgot").click(function(e) {
            $("#form-log").addClass("hide"); 
            $("#form-forgot").addClass("active"); 
        });

       
        $(".back").click(function(e) {
           console.log(e.target);

            $("#form-log").removeClass("hide"); 
            $("#form-forgot").removeClass("active"); 
        });
    });
    </script>

</body>


<script>
    function togglePassword(element) {
        var x = element.previousElementSibling ;

        if (x.type === "password") {
            x.type = "text";
            element.classList.remove('fa-eye');
            element.classList.add('fa-eye-slash');
        } else {
            x.type = "password";
            element.classList.remove('fa-eye-slash');
            element.classList.add('fa-eye');
        }
    }
</script>

<style>
  .login-dark form.no-transition {
      transition: none !important;
  }
  .forgot, .back {
    cursor: pointer;
  }
    .login-dark {
    overflow: hidden;
    height:100vh;
    background:#475d62 url("/imgs/annie-spratt-OB-rBuFoAzY-unsplash.jpg");
    background-size:cover;
    background-position: center;
    position:relative;
  }

  .login-dark form {
    max-width:320px;
    width:90%;
    background-color:#f5f5f5;
    padding:40px;
    border-radius:4px;
    
    position:absolute;
    top:50%;
    left:50%;
    color:#000;
    box-shadow:3px 3px 4px rgba(0,0,0,0.2);
    transform:translate(-50%, -50%);

  }

  #form-log {
    opacity: 1;
    transition: 1s ease;

    
  }

  /* #form-forgot {
    transform:translate(100%, -50%);
    right: 0%;
    left: unset;
    opacity: 0;
    transition: 1s ease;

  } */

  

  #form-log.hide {
    transform: translate(-100%, -50%) !important;
    left: 0;
    opacity: 0;
  }

  #form-forgot.active {

    transform:translate(50%, -50%);
    right: 50%;
    /* right: unset; */
    opacity: 1;
  }

  .login-dark .illustration {
    text-align:center;
    padding:15px 0 20px;
    font-size:100px;
    color:#2980ef;
  }

  .login-dark form .form-control {
    background:none;
    border:none;
    border-bottom:1px solid #434a52;
    border-radius:0;
    box-shadow:none;
    outline:none;
    color:inherit;
  }

  .login-dark form .btn-primary {
    background:#214a80;
    border:none;
    border-radius:4px;
    padding:11px;
    box-shadow:none;
    margin-top:26px;
    text-shadow:none;
    outline:none;
  }

  .login-dark form .btn-primary:hover, .login-dark form .btn-primary:active {
    background:#214a80;
    outline:none;
  }

  .login-dark form .forgot {
    display:block;
    text-align:center;
    font-size:12px;
    color:#6f7a85;
    opacity:0.9;
    text-decoration:none;
  }

  .login-dark form .forgot:hover, .login-dark form .forgot:active {
    opacity:1;
    text-decoration:none;
  }

  .login-dark form .btn-primary:active {
    transform:translateY(1px);
  }
</style>

<script>
    $(window).on('load', function () {
        $('.loading-wrapper').fadeOut('slow');
    });
</script>

</html>