/* 
 *  Build on pojay.dev @42A
 */


function getFileIcon(fname) {
  if (fname === "" || fname === null) {
    return "";
  }
  var ext = fname.split(".");
  if (ext.length === 1 || (ext[0] === "" && ext.length === 2)) {
    return "";
  }
  ext = ext.pop();
  var icon = "styles/images/extensions/" + ext + ".png";
  return icon;
}

jQuery(document).ready(function () {
  jQuery("a[id^='rmRow_']").live("click", function () {
    if (confirm("Are you sure to delete this data ?")) {
      var id = jQuery(this).attr("id").split("_")[1];
      jQuery.ajax({
        url: sajax, data: { mode: "del", "id": id }, success: function (data) {
          ot.fnReloadAjax(sajax + "&json=1");
        }
      });
    }
  });
});


function updateQueryString(key, value, url) {
  if (!url)
    url = window.location.href;
  var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
    hash;

  if (re.test(url)) {
    if (typeof value !== 'undefined' && value !== null)
      return url.replace(re, '$1' + key + "=" + value + '$2$3');
    else {
      hash = url.split('#');
      url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
      if (typeof hash[1] !== 'undefined' && hash[1] !== null)
        url += '#' + hash[1];
      return url;
    }
  }
  else {
    if (typeof value !== 'undefined' && value !== null) {
      var separator = url.indexOf('?') !== -1 ? '&' : '?';
      hash = url.split('#');
      url = hash[0] + separator + key + '=' + value;
      if (typeof hash[1] !== 'undefined' && hash[1] !== null)
        url += '#' + hash[1];
      return url;
    }
    else
      return url;
  }
}

function removeParameter(url, parameter) {
  var urlparts = url.split('?');

  if (urlparts.length >= 2) {
    var urlBase = urlparts.shift(); //get first part, and remove from array
    var queryString = urlparts.join("?"); //join it back up

    var prefix = encodeURIComponent(parameter) + '=';
    var pars = queryString.split(/[&;]/g);
    for (var i = pars.length; i-- > 0;)               //reverse iteration as may be destructive
      if (pars[i].lastIndexOf(prefix, 0) !== -1)   //idiom for string.startsWith
        pars.splice(i, 1);
    url = urlBase + '?' + pars.join('&');
  }
  return url;
}