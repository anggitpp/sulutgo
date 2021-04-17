/* 
 *  Build on pojay.dev @42A
 */
function removeParameter(url, parameter)
{
  var urlparts = url.split('?');

  if (urlparts.length >= 2)
  {
    var urlBase = urlparts.shift(); //get first part, and remove from array
    var queryString = urlparts.join("?"); //join it back up

    var prefix = encodeURIComponent(parameter) + '=';
    var pars = queryString.split(/[&;]/g);
    for (var i = pars.length; i-- > 0; )               //reverse iteration as may be destructive
      if (pars[i].lastIndexOf(prefix, 0) !== -1)   //idiom for string.startsWith
        pars.splice(i, 1);
    url = urlBase + '?' + pars.join('&');
  }
  return url;
}