function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }

$( document ).ready(function() {
    if(!getCookie("cookies_consent")) {
        var frames = window.frames;
        var i;

        for (i = 0; i < frames.length; i++) {
            frames[i].location = "/templates/rbpat/assets/ban.html";
        }
    }
});

$(".input-password-toggle").click(function() {
   $(this).children('span').toggleClass("fa-eye fa-eye-slash");
});