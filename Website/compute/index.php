<!-- Caladis calculation page -->
<!-- Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. -->

<!DOCTYPE html>
<html lang="en">

	<?php include( $_SERVER['DOCUMENT_ROOT'] . "/elements/head.php"); ?>

	<body>
        <!-- this html serves as a wrapper for the calculation process -->

		<?php include( $_SERVER['DOCUMENT_ROOT'] . "/elements/header.php"); ?>

		<div class="container">
			<div class="indent">
				<?php
					require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/build.class.php");
					$buildClass = new build;
					$buildClass->qd();
				?>
			</div>
		</div>

		<div class="container">
			<div id="output-area" class="area">
				<div class="stage loading"></div>

				<div class="col-left">
					<div class="stage loading">
					</div>
				</div>
				<div class="col-right">
					<div class="stage loading">
					</div>
				</div>
			</div>
		</div>


		<?php include( $_SERVER['DOCUMENT_ROOT'] . "/elements/footer.php"); ?>

        
        <script type="text/javascript">
			var paramStr = window.location.href.split("?")[1];
			if(!paramStr) window.location.href = "../";

            $.ajax({
                type: "GET",
                url: "ajax.php",
                data: paramStr,
                cache: false,
                beforeSend: function(html){},
                success: function(html){
                    $("#output-area").html(html);
                },
                error:function (xhr, ajaxOptions, thrownError){
if(thrownError == '') $("#output-area").html("<div class='error'><p>Error: an unknown error occurred. Sorry!</p></div>");
else $("#output-area").html("<div class='error'><p>Error: " + thrownError + "</p></div>");
                }  
            });
        </script>
        
	</body>
</html>

