<?php

function encodeToken($token) {
  // split into 4 parts
  $part1 = substr($token, 0, 16);
  $part2 = substr($token, 16, 32);
  $part3 = substr($token, 32, 48);
  $part4 = substr($token, 48, 64);

  // join the parts after encoding them saparated by |
  $encodedToken = base64_encode($part1) . "|" . base64_encode($part2) . "|" . base64_encode($part3) . "|" . base64_encode($part4);
  
  echo $encodedToken;
  
  return 'storage.token=atob("'.$encodedToken.'".split("|")[0]) + atob("'.$encodedToken.'".split("|")[1]) + atob("'.$encodedToken.'".split("|")[2]) + atob("'.$encodedToken.'".split("|")[3])';

}

 encodeToken('b03qdo76qb3hcaiagdl53ghqjpv1orcnfbjmhljqf7mcpq7e59bdnqfoe00j1t50');

?>