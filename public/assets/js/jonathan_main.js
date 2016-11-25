/*-----------------------------------------------------------------------------------*/
/* 		Mian Js Start ( Drive Me - Driving School Management HTML5 Theme )
 /*-----------------------------------------------------------------------------------*/
$(document).ready(function ($) {
    "use strict"

    /*-----------------------------------------------------------------------------------*/
    /*   PRELOADER
     /*-----------------------------------------------------------------------------------*/
    $(window).bind("load", function () {
        $('.work-in-progress').fadeOut(100);
    });

    /*-----------------------------------------------------------------------------------*/
    /*    STICKY NAVIGATION
     /*-----------------------------------------------------------------------------------*/
    $(".sticky").sticky({topSpacing: 0});
    /*-----------------------------------------------------------------------------------*/
    /*    DATE PICKER
     /*-----------------------------------------------------------------------------------*/
    $("#datepicker").datepicker({
        inline: true
    });
    /*-----------------------------------------------------------------------------------*/
    /*  ISOTOPE PORTFOLIO
     /*-----------------------------------------------------------------------------------*/
    var $container = $('.portfolio-wrapper .items');
    $container.imagesLoaded(function () {
        $container.isotope({
            itemSelector: '.item',
            layoutMode: 'fitRows'
        });
    });
    $('.filter li a').on("click", function () {
        $('.filter li a').removeClass('active');
        $(this).addClass('active');
        var selector = $(this).attr('data-filter');
        $container.isotope({
            filter: selector
        });
        return false;
    });

    /*-----------------------------------------------------------------------------------*/
    /* 	BANNER SLIDER
     /*-----------------------------------------------------------------------------------*/
    $('.flexslider').flexslider({
        animation: "fade",
        slideshow: true, //Boolean: Animate slider automatically
        slideshowSpeed: 6000, //Integer: Set the speed of the slideshow cycling, in milliseconds
        animationSpeed: 400, //Integer: Set the speed of animations, in milliseconds
        pauseOnAction: true, //Boolean: Pause the slideshow when interacting with control elements, highly recommended.
        pauseOnHover: true            //Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
    });
    /*-----------------------------------------------------------------------------------*/
    /* 	WOW ANIMATION
     /*-----------------------------------------------------------------------------------*/
    var wow = new WOW({
        boxClass: 'wow', // animated element css class (default is wow)
        animateClass: 'animated', // animation css class (default is animated)
        offset: 10, // distance to the element when triggering the animation (default is 0)
        mobile: false, // trigger animations on mobile devices (default is true)
        live: true       // act on asynchronously loaded content (default is true)
    });
    wow.init();
    /*-----------------------------------------------------------------------------------*/
    /*    Parallax
     /*-----------------------------------------------------------------------------------*/
    jQuery.stellar({
        horizontalScrolling: false,
        scrollProperty: 'scroll',
        positionProperty: 'position'
    });
    /*-----------------------------------------------------------------------------------*/
    /* SCROLL TO TOP
     /*-----------------------------------------------------------------------------------*/
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.scroll-top').fadeIn();
        } else {
            $('.scroll-top').fadeOut();
        }
    });
    //Click event to scroll to top
    $('.scroll-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 800);
        return false;
    });
    $('.theme-title').click(function () {
        $('html, body').animate({scrollTop: 0}, 800);
        return false;
    });
    $('.text-slider .get-form').click(function (event) {
        event.preventDefault();
        if ((jQuery('header').hasClass('sticky')))
        {
            $('html, body').animate({scrollTop: jQuery("#find-course").offset().top - 150}, 1e3);
        }
        else{
            $('html, body').animate({scrollTop: jQuery("#find-course").offset().top}, 1e3);
        }
    });

});
/*-----------------------------------------------------------------------------------*/
/* 	DROPDOWN HOVER
 /*-----------------------------------------------------------------------------------*/
/*$('.navbar .dropdown').on("hover",function() {
 $(this).find('.dropdown-menu').first().stop(true, true).slideDown(0);
 },
 function() {
 $(this).find('.dropdown-menu').first().stop(true, true).slideUp(0)
 });*/
/*-----------------------------------------------------------------------------------*/
/*    TESTIMONIALS SLIDER
 /*-----------------------------------------------------------------------------------*/
$(".testi-slide").owlCarousel({
    autoPlay: 6000, //Set AutoPlay to 6 seconds 
    items: 1,
    singleItem: true,
    autoPlay : true,
            navigation: false, // Show next and prev buttons
    pagination: true,
    navigationText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"]
});
/*-----------------------------------------------------------------------------------*/
/*    NEWS SLIDER
 /*-----------------------------------------------------------------------------------*/
$('.news-slide').lightSlider({
    item: 2,
    slideMargin: 15,
    adaptiveHeight: true,
    slideMove: 2, // slidemove will be 1 if loop is true
    speed: 400
});
/*-----------------------------------------------------------------------------------*/
/*    RELATED SLIDER
 /*-----------------------------------------------------------------------------------*/
$('.raleted-slide').lightSlider({
    item: 1,
    slideMargin: 0,
    adaptiveHeight: true,
    slideMove: 1, // slidemove will be 1 if loop is true
    speed: 400
});
/*-----------------------------------------------------------------------------------*/
/*    SLIDER
 /*-----------------------------------------------------------------------------------*/
$(".owl-slide").owlCarousel({
    autoPlay: 6000, //Set AutoPlay to 6 seconds 
    items: 1,
    singleItem: true,
    autoPlay : false,
            navigation: true, // Show next and prev buttons
    pagination: true,
    navigationText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"]
});
/*-----------------------------------------------------------------------------------*/
/*    POPUP VIDEO
 /*-----------------------------------------------------------------------------------*/
$('.popup-vedio').magnificPopup({
    type: 'inline',
    fixedContentPos: false,
    fixedBgPos: true,
    overflowY: 'auto',
    closeBtnInside: true,
    preloader: true,
    midClick: true,
    removalDelay: 300,
    mainClass: 'my-mfp-slide-bottom'
});
$('.gallery-pop').magnificPopup({
    delegate: 'a',
    type: 'image',
    tLoading: 'Loading image #%curr%...',
    mainClass: 'mfp-img-mobile',
    gallery: {
        enabled: true,
        navigateByImgClick: true,
        preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
    },
    image: {
        tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
        titleSrc: function (item) {
            return item.el.attr('title') + '';
        }}
});
function validateForm() {
    var x = document.forms["newsletter"]["newsletter"].value;
    if (x == null || x == "") {
        alert("Name must be filled out");
        return false;
    }
}
/*-----------------------------------------------------------------------------------*/
/*    CONTACT FORM
 /*-----------------------------------------------------------------------------------*/
/*
 function checkmail(input){
 var pattern1=/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
 if(pattern1.test(input)){ return true; }else{ return false; }}     
 function proceed(){
 var name = document.getElementById("name");
 var email = document.getElementById("email");
 var company = document.getElementById("company");
 var web = document.getElementById("website");
 var msg = document.getElementById("message");
 var errors = "";
 if(name.value == ""){ 
 name.className = 'error';
 return false;}    
 else if(email.value == ""){
 email.className = 'error';
 return false;}
 else if(checkmail(email.value)==false){
 alert('Please provide a valid email address.');
 return false;}
 else if(company.value == ""){
 company.className = 'error';
 return false;}
 else if(web.value == ""){
 web.className = 'error';
 return false;}
 else if(msg.value == ""){
 msg.className = 'error';
 return false;}
 else 
 {
 $.ajax({
 type: "POST",
 url: "submit.php",
 data: $("#contact_form").serialize(),
 success: function(msg){
 //alert(msg);
 if(msg){
 $('#contact_form').fadeOut(1000);
 $('#contact_message').fadeIn(1000);
 document.getElementById("contact_message");
 return true;
 }}
 });
 }};
 */

// Content Contact Form
// ---------------------------------------------------------------------------------------
$(function () {
    if ($("#contact_form").length) {
        $("#contact_form .form-control").tooltip({placement: 'top', trigger: 'manual'}).tooltip('hide');
        $('#contact_form .form-control').blur(function () {
            $(this).tooltip({placement: 'top', trigger: 'manual'}).tooltip('hide');
        });

        $("#contact_form #btn_submit").click(function () {
            // validate and process form
            // first hide any error messages
            //$('#contact_form .error').hide();

            // name field
            var name = $("#contact_form input#name").val();
            if (name == "" || name == "Name") {
                $("#contact_form input#name").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#contact_form input#name").focus();
                $("#contact_form input#name").addClass('highlight');
                return false;
            } else {
                $("#contact_form input#name").removeClass('highlight');
            }

            // email field
            var email = $("#contact_form input#email").val();
            var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
            //console.log(filter.test(email));
            if (!filter.test(email)) {
                $("#contact_form input#email").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#contact_form input#email").focus();
                $("#contact_form input#email").addClass('highlight');
                return false;
            } else {
                $("#contact_form input#email").removeClass('highlight');
            }

            // company, website field
            var company = $("#contact_form input#company").val();
            var website = $("#contact_form input#website").val();

            // message textarea
            var message = $("#contact_form textarea#message").val();
            if (message == "" || message == "Message") {
                $("#contact_form textarea#message").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#contact_form textarea#message").focus();
                $("#contact_form textarea#message").addClass('highlight');
                return false;
            } else {
                $("#contact_form textarea#message").removeClass('highlight');
            }

            var dataString = 'name=' + name + '&email=' + email + '&company=' + company + '&website=' + website + '&message=' + message;
            //alert (dataString);return false;

            $.ajax({
                type: "POST",
                url: "php/contact-form.php",
                data: dataString,
                success: function () {
                    $('#contact_form').prepend("<div class=\"alert alert-success fade in\"><button class=\"close\" data-dismiss=\"alert\" type=\"button\">&times;</button><strong>Contact Form Submitted!</strong> We will be in touch soon.</div>");
                    $('#contact_form')[0].reset();
                }
            });
            return false;
        });
    }
});

// Find course form
// ---------------------------------------------------------------------------------------
$(function () {
    if ($("#find_course").length) {
        $("#find_course .form-control").tooltip({placement: 'top', trigger: 'manual'}).tooltip('hide');
        $('#find_course .form-control').blur(function () {
            $(this).tooltip({placement: 'top', trigger: 'manual'}).tooltip('hide');
        });

        $("#find_course #btn_submit").click(function () {
            // validate and process form
            // first hide any error messages
            //$('#find_course .error').hide();

            // name field
            var name = $("#find_course input#name").val();
            if (name == "" || name == "Name") {
                $("#find_course input#name").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#find_course input#name").focus();
                $("#find_course input#name").addClass('highlight');
                return false;
            } else {
                $("#find_course input#name").removeClass('highlight');
            }

            // email field
            var email = $("#find_course input#email").val();
            var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
            //console.log(filter.test(email));
            if (!filter.test(email)) {
                $("#find_course input#email").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#find_course input#email").focus();
                $("#find_course input#email").addClass('highlight');
                return false;
            } else {
                $("#find_course input#email").removeClass('highlight');
            }

            // phone
            var phone = $("#find_course input#phone").val();
            if (phone == "" || phone == "123") {
                $("#find_course input#phone").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#find_course input#phone").focus();
                $("#find_course input#phone").addClass('highlight');
                return false;
            } else {
                $("#find_course input#phone").removeClass('highlight');
            }

            // date
            var date = $("#find_course input#datepicker").val();
            if (date == "" || date == "") {
                $("#find_course input#datepicker").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#find_course input#datepicker").focus();
                $("#find_course input#datepicker").addClass('highlight');
                return false;
            } else {
                $("#find_course input#datepicker").removeClass('highlight');
            }

            // select1
            var select1 = $("#find_course select#select1").val();
            if (select1 == "" || select1 == "Course Type") {
                $("#find_course select#select1").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#find_course select#select1").focus();
                $("#find_course select#select1").addClass('highlight');
                return false;
            } else {
                $("#find_course input#select1").removeClass('highlight');
            }

            // select2
            var select2 = $("#find_course select#select2").val();
            if (select2 == "" || select2 == "Car Type") {
                $("#find_course select#select2").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#find_course select#select2").focus();
                $("#find_course select#select2").addClass('highlight');
                return false;
            } else {
                $("#find_course input#select2").removeClass('highlight');
            }

            // select3
            var select3 = $("#find_course select#select3").val();
            if (select3 == "" || select3 == "Time") {
                $("#find_course select#select3").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#find_course select#select3").focus();
                $("#find_course select#select3").addClass('highlight');
                return false;
            } else {
                $("#find_course input#select3").removeClass('highlight');
            }


            var dataString = 'name=' + name + '&email=' + email + '&phone=' + phone + '&date=' + date + '&select1=' + select1 + '&select2=' + select2 + '&select3=' + select3;
            //alert (dataString);return false;

            $.ajax({
                type: "POST",
                url: "php/find-course-form.php",
                data: dataString,
                success: function () {
                    $('#find_course').prepend("<div class=\"alert alert-success fade in\"><button class=\"close\" data-dismiss=\"alert\" type=\"button\">&times;</button><strong>Contact Form Submitted!</strong> We will be in touch soon.</div>");
                    $('#find_course')[0].reset();
                    window.location.href = "05_courses-list.html";
                }
            });
            return false;
        });
    }
});

// paypal payment
// ---------------------------------------------------------------------------------------
$(function () {
    if ($("#user_billing").length) {
        $("#user_billing .form-control").tooltip({placement: 'top', trigger: 'manual'}).tooltip('hide');
        $('#user_billing .form-control').blur(function () {
            $(this).tooltip({placement: 'top', trigger: 'manual'}).tooltip('hide');
        });

        $("#paypal_payment").click(function () {
            // validate and process form
            // first hide any error messages
            //$('#find_course .error').hide();

            // name field
            var name = $("#user_billing input#ub_name").val();
            if (name == "" || name == "Name") {
                $("#user_billing input#ub_name").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#user_billing input#ub_name").focus();
                $("#user_billing input#ub_name").addClass('highlight');
                return false;
            } else {
                $("#user_billing input#ub_name").removeClass('highlight');
            }

            // surname field
            var surname = $("#user_billing input#ub_name_surname").val();
            if (surname == "" || surname == "Surname") {
                $("#user_billing input#ub_name_surname").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#user_billing input#ub_name_surname").focus();
                $("#user_billing input#ub_name_surname").addClass('highlight');
                return false;
            } else {
                $("#user_billing input#ub_name_surname").removeClass('highlight');
            }

            // phone field
            var phone = $("#user_billing input#ub_phone").val();
            if (phone == "" || phone == "Your Phone") {
                $("#user_billing input#ub_phone").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#user_billing input#ub_phone").focus();
                $("#user_billing input#ub_phone").addClass('highlight');
                return false;
            } else {
                $("#user_billing input#ub_name_surname").removeClass('highlight');
            }

            // email field
            var email = $("#user_billing input#ub_email").val();
            var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
            //console.log(filter.test(email));
            if (!filter.test(email)) {
                $("#user_billing input#ub_email").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#user_billing input#ub_email").focus();
                $("#user_billing input#ub_email").addClass('highlight');
                return false;
            } else {
                $("#user_billing input#ub_email").removeClass('highlight');
            }

            // course name
            var course_name = $("#user_billing select#ub_course_name option:selected").text();
            var course_name_id = $("#user_billing select#ub_course_name").val();
            if (course_name == "" || course_name == "Course Name") {
                $("#user_billing select#ub_course_name").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#user_billing select#ub_course_name").focus();
                $("#user_billing select#ub_course_name").addClass('highlight');
                return false;
            } else {
                $("#user_billing input#ub_course_name").removeClass('highlight');
            }

            // course date
            var date = $("#user_billing input#datepicker").val();
            if (date == "" || date == "DD/MM/YY") {
                $("#user_billing input#datepicker").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#user_billing input#datepicker").focus();
                $("#user_billing input#datepicker").addClass('highlight');
                return false;
            } else {
                $("#user_billing input#datepicker").removeClass('highlight');
            }

            // message textarea
            var message = $("#user_billing textarea#ub_message").val();

            var dataString = 'name=' + name + '&surname=' + surname + '&phone=' + phone + '&email=' + email + '&course_name=' + course_name + '&date=' + date + '&message=' + message;
            //alert (dataString);return false;

            $.ajax({
                type: "POST",
                url: "php/paypal-payment.php",
                data: dataString,
                success: function () {
                    $('#user_billing').prepend("<div class=\"alert alert-success fade in\"><button class=\"close\" data-dismiss=\"alert\" type=\"button\">&times;</button><strong>Contact Form Submitted!</strong> We will be in touch soon.</div>");
                    $('#user_billing')[0].reset();
                    window.location.href = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=' + course_name_id;
                }
            });
            return false;
        });
    }
});

// Offline payment
// ---------------------------------------------------------------------------------------
$(function () {
    if ($("#user_billing").length) {
        $("#user_billing .form-control").tooltip({placement: 'top', trigger: 'manual'}).tooltip('hide');
        $('#user_billing .form-control').blur(function () {
            $(this).tooltip({placement: 'top', trigger: 'manual'}).tooltip('hide');
        });

        $("#offline_payment").click(function () {
            // validate and process form
            // first hide any error messages
            //$('#find_course .error').hide();

            // name field
            var name = $("#user_billing input#ub_name").val();
            if (name == "" || name == "Name") {
                $("#user_billing input#ub_name").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#user_billing input#ub_name").focus();
                $("#user_billing input#ub_name").addClass('highlight');
                return false;
            } else {
                $("#user_billing input#ub_name").removeClass('highlight');
            }

            // surname field
            var surname = $("#user_billing input#ub_name_surname").val();
            if (surname == "" || surname == "Surname") {
                $("#user_billing input#ub_name_surname").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#user_billing input#ub_name_surname").focus();
                $("#user_billing input#ub_name_surname").addClass('highlight');
                return false;
            } else {
                $("#user_billing input#ub_name_surname").removeClass('highlight');
            }

            // phone field
            var phone = $("#user_billing input#ub_phone").val();
            if (phone == "" || phone == "Your Phone") {
                $("#user_billing input#ub_phone").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#user_billing input#ub_phone").focus();
                $("#user_billing input#ub_phone").addClass('highlight');
                return false;
            } else {
                $("#user_billing input#ub_name_surname").removeClass('highlight');
            }

            // email field
            var email = $("#user_billing input#ub_email").val();
            var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
            //console.log(filter.test(email));
            if (!filter.test(email)) {
                $("#user_billing input#ub_email").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#user_billing input#ub_email").focus();
                $("#user_billing input#ub_email").addClass('highlight');
                return false;
            } else {
                $("#user_billing input#ub_email").removeClass('highlight');
            }

            // course name
            //var course_name = $("#user_billing select#ub_course_name").val();
            var course_name = $("#user_billing select#ub_course_name option:selected").text();
            if (course_name == "" || course_name == "Course Name") {
                $("#user_billing select#ub_course_name").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#user_billing select#ub_course_name").focus();
                $("#user_billing select#ub_course_name").addClass('highlight');
                return false;
            } else {
                $("#user_billing input#ub_course_name").removeClass('highlight');
            }

            // course date
            var date = $("#user_billing input#datepicker").val();
            if (date == "" || date == "DD/MM/YY") {
                $("#user_billing input#datepicker").tooltip({placement: 'bottom', trigger: 'manual'}).tooltip('show');
                $("#user_billing input#datepicker").focus();
                $("#user_billing input#datepicker").addClass('highlight');
                return false;
            } else {
                $("#user_billing input#datepicker").removeClass('highlight');
            }

            // message textarea
            var message = $("#user_billing textarea#ub_message").val();

            var dataString = 'name=' + name + '&surname=' + surname + '&phone=' + phone + '&email=' + email + '&course_name=' + course_name + '&date=' + date + '&message=' + message;
            //alert (dataString);return false;

            $.ajax({
                type: "POST",
                url: "php/offline-payment.php",
                data: dataString,
                success: function () {
                    $('#user_billing').prepend("<div class=\"alert alert-success fade in\"><button class=\"close\" data-dismiss=\"alert\" type=\"button\">&times;</button><strong>Contact Form Submitted!</strong> We will be in touch soon.</div>");
                    $('#user_billing')[0].reset();
                }
            });
            return false;
        });
    }
});