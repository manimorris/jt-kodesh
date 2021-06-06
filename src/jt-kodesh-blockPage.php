<?php


	// Set image url's '/wp-content/plugins/jt-kodesh/src/background.jpg'
	/**
	 * @param $path
	 * @return string
	 */
	function imageEncodeURL($path)
	{
		$image = file_get_contents($path);
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$type  = $finfo->buffer($image);
		return "data:".$type.";charset=utf-8;base64,".base64_encode($image);
	}
	
	add_action( 'wp_footer', function(){ 
			
		// Get Kodesh details
		$details = get_jt_cookie();
		
		// Repair!!//
		if(!isset($details["day_name"]) || $details["day_name"] = false) {
				$details["day_name"] = "Testing Date";
				$details["ends"] = strtotime("+ 32 seconds", time());
		}
		?>
		<html>
		 
			<!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
			   -->

			<?php
			
				echo " <link rel='stylesheet' href=' " . plugins_url('/css/bootstrap4.css', __FILE__) . "' > ";
				echo "<link rel='stylesheet' href=' " .plugins_url('/css/bootstrap-theme4.css', __FILE__). "' > ";
				echo "<script src=' " .plugins_url('/js/bootstrap4.js', __FILE__). "' > </script>";

			
			
			 
			 ?>
			<style>
			  .custom-body {
				height: 80vh;
				overflow-y: auto; 
				background-image: url(<?php echo imageEncodeURL(__DIR__.'/background.jpg') ?>);
				background-repeat: no-repeat;
				background-size: 75vh;
				background-position: center;
				 text-align: center;
				font-family: Assistant, Arial, Helvetica, sans-serif;
				}
			  .modal-content{
				z-index: 9999999;
			  }
			  
			  .custom-body-text{
				margin-top: 3%;
			  }
			  
			  .msgTitle{
				  color:rgba(42, 68, 110);
				  font-size: 3rem;
				  font-weight: bold;
			  }
			  
			  .msgText{
				  color:#ae8319;
				  font-size: 1.5rem;
				  font-weight: bold;
			  }
			  
			  .m-body-s{
				  justify-content: center;
			  }
			  .timer-class {
				   text-align: center;
			  }
			  
			</style>


		<!-- Modal -->
		<div class="modal fade" id="timerPopup" tabindex="-1" role="dialog" aria-labelledby="jt-kodesh-popup" aria-hidden="true">
		  <div class="modal-dialog  modal-lg" role="document">
			<div class="modal-content">
			  <div class="modal-header">
			   
			
			
			</div>
				<div class="modal-body  bg-image custom-body d-flex m-body-s"  >	
				
					<div class="col-8 custom-body-text">
						<div class="text-center ">
							<h1 class='msgTitle '><?php _e('Shabbat Shalom', 'Jt-Kodesh') ?></h1>
							<p class="msgText" ><?php _e('This website respects Shabbat and the Jewish holidays', 'Jt-Kodesh') ?></p>
							<p class="msgText" ><?php _e('The website will return to activity immediately after the Shabbat ends', 'Jt-Kodesh') ?></p>
						</div>
						
						<div id="timer" class="timer-class msgTitle">
							<h4><?php _e('The website will be back in ', 'Jt-Kodesh') ?></h4>
							<h4 id='cTimer'></h4> 
							<BR>
						</div>
				
					</div>
				</div>
			  
			  <div class="modal-footer d-inline-block">		
				<div id="jt-footer" class="text-left">
					
					<a href="https:\\www.jewtech.co.il">
						<img src='<?php echo imageEncodeURL(__DIR__.'/jewtech.png')?>' class=""  >
						<span>This Plugin is powerd by JewTech		</span>
					</a>
					
				</div>
				
			  </div>
			</div>
		  </div>
		</div>

		<script>

			jQuery( document ).ready( function( $ ) {
				// $() will work as an alias for jQuery() inside of this function
				$('#timerPopup').modal({
					backdrop: 'static',
					keyboard: false
				});
				$('#timerPopup').modal('show') ;
		  

		   
				  // Set the date we're counting down to
				var countDownDate = <?php echo  $details["ends"] ?> * 1000;

				// Update the count down every 1 second
				var x = setInterval(function() {

					// Get today's date and time
					var now = new Date().getTime();
				
					// Find the distance between now and the count down date
					var distance = countDownDate - now;
						
					// Time calculations for days, hours, minutes and seconds
					var days = Math.floor(distance / (1000 * 60 * 60 * 24));
					var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
					var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
					var seconds = Math.floor((distance % (1000 * 60)) / 1000);
						
					// Output the result in an element with id='cTimer'
					document.getElementById('cTimer').innerHTML = days + ' ימים , ' + hours + ' שעות , '
						+ minutes + ' דקות , ' + seconds + ' שניות ';
						
					// If the count down is over, write some text 
					if (distance < 0) {
						clearInterval(x);
						$( '.modal' ).modal( 'hide' ).data( 'bs.modal', null );
						
					}

				}, 1000); 
				
			} );
		</script>

		</html>
	<?php }); ?>
