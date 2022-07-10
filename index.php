<!doctype html>
<html>
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-JNPCQR0PF1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-JNPCQR0PF1');
</script>

<meta charset="utf-8">
<link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon"> 
<title>AUSAM: ACGME Annual Update Scholarly Activity Monitor v2</title>
<link href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="css/index.css" rel="stylesheet" type="text/css">
<script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="js/index.js"></script>
</head>

<body>
<div id="page">
<div id="header"><img src="img/beta.png" width="60" height="60" alt=""/>
<p>AUSAM: ACGME Annual Update Scholarly Activity Monitor <a href="ausam2_instructions.pdf">(Instructions)</a></p>
</div>
<div id="content">
    <div id="form_div">
        <form id="adsform" name="adsform" method="post">
            <p>
            <textarea rows="10" name="adstext" id="adstext" placeholder="Paste block of HTML code from ADS" required></textarea>
            </p>
            <div id="progressbar"><div class="progress-label">Loading...</div></div>
            <p><label for="acyr">Applicable academic year:</label>
            <select name="acyr" id="acyr" data-mini="true" data-native-menu="false">
              <?php 
date_default_timezone_set("America/Detroit");
for ($i=date('Y')-2; $i<date('Y')+1; $i++) {
  echo "\t<option value='$i'";
	if ($i==(date('Y')-1)) echo " selected";
	echo ">$i-".($i+1)."</option>\r\n";
}
?>
            </select>
            &nbsp;
            <input type="checkbox" id="pmid_only" value="1"> <label for="pmid_only">Check PMIDs Only</label>
              <p>&nbsp;</p>
            <input type="button" name="doit" id="doit" value="Check Data">
		    <input type="reset" name="clear" id="clear" value="Reset">
        </form>
    </div>
    <div id="results">
	  <h2>Results</h2>
      <div id="tabs">
      <ul>
      <li><a href="#pmid">PMID Check<span id="pmid_icon"></span></a></li>
      <li><a href="#license">State License Check<span id="license_icon"></span></a></li>
      <!-- <li><a href="#boards">Board Certification Check<span id="boards_icon"></span></a></li> -->
      </ul>
        <div id="pmid">
  <table border="0" cellpadding="0" cellspacing="0" id="pmid_table">
  <thead>
    <tr>
      <th scope="col" style="width: 8em">PMID</th>
      <th scope="col">Date Published</th>
      <th scope="col">Authors</th>
      <th scope="col">Title</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
	<p>Suspect PMIDs will be flagged <strong style="color:#F00">red</strong> or <strong style="color:#CBD000">yellow</strong>. All-white background indicates no errors were detected</p>
        </div>
        <div id="license">
        <table border="0" cellpadding="0" cellspacing="0" id="lic_table">
        <tbody>
        </tbody>
        </table>
        </div>
        <div id="boards">
          <table border="0" cellpadding="0" cellspacing="0" id="board_table">
          <thead>
            <tr>
              <th scope="col">Faculty</th>
              <th scope="col">Specialty</th>
              <th scope="col">Notes</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
	<!-- <p>Suspect board certifications will be flagged <strong style="color:#F00">red</strong> or <strong style="color:#CBD000">yellow</strong>. All-white background indicates no errors were detected</p> -->
        </div>
       </div>
    </div>
</div>
</div>
<footer id="footer">
<p>Please use at your own risk. Coders, see <a href="https://github.com/Trynomial-Solutions/AUSAM2">GitHub Repo</a>. Contact <a href="mailto:ngoyal1@hfhs.org">Nikhil Goyal</a> with questions or feedback</p>
</footer>
</body>
</html>