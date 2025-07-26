
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

//window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example', require('./components/Example.vue'));

// const app = new Vue({
//     el: '#app'
// });
// 
//typical import
import {TweenMax, Power2, TimelineLite, TimelineMax } from "gsap/TweenMax";



  var sidebarMenu = document.getElementById('toggle-menu');

  sidebarMenu.addEventListener('click', hideSidebar );


  function hideSidebar() {
    


    var menu_title = $('.sidebar .heading .inner');
    var main_content = $('.inner-content');
    var sidebar = $('.sidebar');
    var sidebarLinks = $('.vertical-menu  li a');
    var menuText = $('.vertical-menu  li span');
    var menuIcons = $('.vertical-menu  li i');
    var subMenuLinks = $('.sub-menu li a');
    var menuDropIcons = $('.vertical-menu  li .menu-dropdown-icon');
    var toggleMenu = $('#toggle-menu');
    var controller = new TimelineMax();


     if( $('.vertical-menu').hasClass('is-minified') ) {

      controller.to( sidebar, 0.2, {width: '250px', onComplete:updateSidebar}, '-=0.2' )
                .to( sidebarLinks, 0.2, {display: 'block',width: '100%'}, '+=0.1' )
                .to( main_content, 0.2, {marginLeft: '250px'}, '-=0.6')
                .to( menuIcons, 0.2, {marginRight: '13px'}, '-=0.2' )
                .to( subMenuLinks, 0.1, {padding: '10px'}, '-=0.1')
                .to( menuText, 0.2, {delay:0.2, autoAlpha: 1, display: 'block'}, '-=0.3' )
                .to( menuDropIcons, 0.2, {autoAlpha: 1, display: 'block'}, '-=0.2' )
     } else {

      controller
                .to( menuText, 0.2, {autoAlpha: 0, display: 'none'}, '-=0.2' )
                .to( subMenuLinks, 0.1, {padding: 0}, '-=0.1')
                .to( menuDropIcons, 0.2, {autoAlpha: 0, display: 'none'}, '-=0.2' )
                .to( sidebar, 0.2, {width: '55px', onComplete:updateSidebar}, '-=0.2' )
                .to( sidebarLinks, 0.2, {display: 'block', width: '40px'}, '-=0.2' )
                .to( main_content, 0.2, {marginLeft: '40px'}, '-=0.2')
                .to( menuIcons, 0.2, {marginRight: 0}, '-=0.2' )

     }


  }

  function updateSidebar() {

     $('.vertical-menu').toggleClass('is-minified');
  }





// $( sidebarMenu ).on('click', function(){
 
//    $(this).toggleClass('center');
//    $('.sidebar .vertical-menu').toggleClass('toggle');
//    //$('.sidebar .vertical-menu span').addClass('toggle');
//    $('.sidebar .heading .inner').toggleClass('is-hide');
//    $('.menu-dropdown-icon').toggleClass('hide');
//    $('.sidebar').toggleClass('toggle');
//    $('.inner-content').toggleClass('toggle');

// });

$('.sidebar').on('click', '.center', function(){
  
    setTimeout(function(){
       $('.sidebar .vertical-menu span').removeClass('toggle');
    }, 300)

});

$('.dropdown-toggle').on('click', function(){
     $(this).closest('li').find('ul').toggleClass('show');
});


$('.vertical-menu .parent-item > a').on('click', function(e){

	e.preventDefault();

	$(this).closest('li').toggleClass('show-submenu');
	$(this).find('.fa-chevron-left').toggleClass('fa-chevron-down');

	$(this).next().slideToggle();

});


$('#menuSearch').on('click', function(e) {

  e.preventDefault();
  
  var controller = new TimelineMax();
  var icon = $(this).find('span');
  var topSearch = $('.top-search-box');

  topSearch.toggleClass('is-show');

   controller
          .to(icon, 0.2, {onComplete: toggleIcon })



    function toggleIcon() {

         if( icon.hasClass('fa-search') ) 
         {
            icon.removeClass('fa-search').addClass('fa-close');
         } 
         else 
         {
           icon.removeClass('fa-close').addClass('fa-search');
           $('#topSearchResults').html('');
           $('.top-pagination-links').html('');
         }

     }

  });


 function mobileMenu() {

      $('#sideMenu').on('click', function(e){

          e.preventDefault();

         if( $(this).find('span').hasClass('fa-close') ) {

            $(this).find('span').removeClass('fa-close').addClass('fa-bars');

         } else {

            $(this).find('span').removeClass('fa-bars').addClass('fa-close');

         }

         $('.sidebar').toggleClass('is-show');

      })

 }
 mobileMenu();