$('.mobClick').click(function() {
  $(this).toggleClass('open');
  $('.sidebar').toggleClass('act');
});

$('.collapse-nav').click(function() {
  $('.site-wrapper').toggleClass('act');
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

