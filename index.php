<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon">
    <title>AUSAM: ACGME Annual Update Scholarly Activity Monitor v2.1</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.min.css" integrity="sha512-okE4owXD0kfXzgVXBzCDIiSSlpXn3tJbNodngsTnIYPJWjuYhtJ+qMoc0+WUwLHeOwns0wm57Ka903FqQKM1sA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="css/index.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div id="page">
        <div id="header"><img src="img/beta.png" width="60" height="60" alt="" />
            <p>AUSAM: ACGME Annual Update Scholarly Activity Monitor <a href="ausam2_instructions.pdf">(Instructions)</a></p>
        </div>
        <div id="content">
            <p style="text-align: center; margin-top: 0.5em"><mark>Update June 2025:</mark> Starting with the 2025-2026 Annual Update, the ACGME <a href="https://acgmehelp.acgme.org/hc/en-us/categories/31772726439447-Annual-Update-2025-2026">removed PMID reporting requirements</a> for faculty. Therefore, this tool will only check PMIDs for residents and fellows. <a href="mailto:info@trynomial.solutions">Feedback</a> is welcome!</p>
            <div id="form_div">
                <form id="adsform" name="adsform" method="post">
                    <p>
                        <textarea rows="10" name="adstext" id="adstext" placeholder="Paste block of HTML code from ADS" required></textarea>
                    </p>
                    <div id="progressbar">
                        <div class="progress-label">Loading...</div>
                    </div>
                    <p><label for="acyr">Applicable academic year:</label>
                        <select name="acyr" id="acyr" data-mini="true" data-native-menu="false">
                            <?php
                            date_default_timezone_set("America/Detroit");
                            for ($i = date('Y') - 2; $i < date('Y') + 1; $i++) {
                                echo "\t<option value='$i'";
                                if ($i == (date('Y') - 1)) echo " selected";
                                echo ">$i-" . ($i + 1) . "</option>\r\n";
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
                </div>
            </div>
        </div>
    </div>
    <footer id="footer">
        <p>Please use at your own risk. Coders, see <a href="https://github.com/Trynomial-Solutions/AUSAM2">GitHub Repo</a>. Contact <a href="mailto:info@trynomial.solutions">Trynomial</a> with questions or feedback</p>
    </footer>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="js/index.js"></script>
</body>

</html>