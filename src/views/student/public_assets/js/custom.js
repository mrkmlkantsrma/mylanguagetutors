$('.selectLanguage, .lang-close').click(function() {
  $('.lang-select').toggleClass('act');
});

$('.mobClick').click(function() {
  $(this).toggleClass('open');
  $('.header-right-bottom').toggleClass('act');
  $('body').toggleClass('navOpen')
});

$('.collapse-nav').click(function() {
  $('.site-wrapper').toggleClass('act');
});

$('.btnNext').click(function() {
  const nextTabLinkEl = $('.nav-tabs .active').closest('li').next('li').find('a')[0];
  const nextTab = new bootstrap.Tab(nextTabLinkEl);
  nextTab.show();
});

$('.btnPrevious').click(function() {
  const prevTabLinkEl = $('.nav-tabs .active').closest('li').prev('li').find('a')[0];
  const prevTab = new bootstrap.Tab(prevTabLinkEl);
  prevTab.show();
});

document.querySelectorAll('a.anchor[href^="#"]').forEach(a => {
  a.addEventListener('click', function (e) {
      e.preventDefault();
      var href = this.getAttribute("href");
      var elem = document.querySelector(href)||document.querySelector("a[name="+href.substring(1, href.length)+"]");
      window.scroll({
          top: elem.offsetTop - 80,
          left: 0,
          behavior: 'smooth'
      });
  });
});

