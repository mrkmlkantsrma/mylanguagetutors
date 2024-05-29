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


// Event listener for dynamic content
document.addEventListener('click', function(event) {
  if (event.target.matches('.copy-btn')) {
      var copyText = event.target.getAttribute('data-link');
      var textarea = document.createElement('textarea');
      textarea.value = copyText;
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand('copy');
      document.body.removeChild(textarea);
      alert('Link copied to clipboard!');
  }
});


