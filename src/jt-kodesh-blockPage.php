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
		
			echo "<link rel='stylesheet' href=' " . plugins_url('/css/bootstrap4.css', __FILE__) . "' > ";
			echo "<link rel='stylesheet' href=' " .plugins_url('/css/bootstrap-theme4.css', __FILE__). "' > ";
			echo "<script src=' " .plugins_url('/js/bootstrap4.js', __FILE__). "' > </script>";
			echo "<link rel='stylesheet' href=' " .plugins_url('/css/jtk-popup.css', __FILE__). "' > ";
		 
		 ?>
		<style>
			.custom-body {
				background-image: url(<?php echo imageEncodeURL(__DIR__.'/background.jpg') ?>);
			}
			
			@media only screen and (max-width: 480px) {
				.custom-body {
					background-image: url(<?php echo imageEncodeURL(__DIR__.'/background-mobile.jpg') ?>);
					height: 95vh;
				}
				.msgText{
					font-size: 1.5rem;
				}
			}
							  
		</style>


	<!-- Modal -->
	<div class="modal fade" id="timerPopup" tabindex="-1" role="dialog" aria-labelledby="jt-kodesh-popup" aria-hidden="true">
	  <div class="modal-dialog  modal-lg" role="document">
		<div class="modal-content ">
		
			<div class="modal-body bg-image custom-body"  >	
			
				<div class="">
					<div class="text-center ">
						<h1 class='msgTitle '><?php esc_html_e('Shabbat Shalom', 'Jt-Kodesh') ?></h1>
						<p class="msgText" ><?php esc_html_e('This website respects Shabbat and the Jewish holidays', 'Jt-Kodesh') ?></p>
						<p class="msgText" ><?php esc_html_e('The website will return to activity immediately after the Shabbat ends', 'Jt-Kodesh') ?></p>
					</div>
					
					<div id="timer" class="msgTitle">
						<h4><?php esc_html_e('The website will be back in ', 'Jt-Kodesh') ?></h4>
						
						<div class="timer-cards">
							<div class="time-card">
								<div class="timer-card-header">
									<?php esc_html_e('Days', 'Jt-Kodesh'); ?>
								</div>
								<div class="time-card-body">
								<p id="Tdays"></p>
								</div>
							</div>	
							
							<div class="time-card">
								<div class="timer-card-header">
									<?php esc_html_e('Hours', 'Jt-Kodesh'); ?>
								</div>
								<div class="time-card-body">
								<p id="Thours"></p>
								</div>
							</div>
							<div class="time-card">
								<div class="timer-card-header">
									<?php esc_html_e('Minutes', 'Jt-Kodesh'); ?>
								</div>
								<div class="time-card-body">
								<p id="Tminutes"></p>
								</div>
							</div>
							<div class="time-card">
								<div class="timer-card-header">
									<?php esc_html_e('Seconds', 'Jt-Kodesh'); ?>
								</div>
								<div class="time-card-body">
								<p id="Tseconds"></p>
								</div>
							</div>	 
						</div>
						<BR>
					</div>
			
				</div>
								
			</div>
		
			<div id="jt-footer">
				
				<a href="https:\\www.jewtech.co.il" style="">
					<img src='<?php echo imageEncodeURL(__DIR__.'/jewtech.png')?>'  >
					<span> This Plugin is powerd by JewTech	</span>
				</a>
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
			var countDownDate = <?php echo $details["ends"] ?> * 1000;

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
					document.getElementById('Tdays').innerHTML = days;
					document.getElementById('Thours').innerHTML = hours;
					document.getElementById('Tminutes').innerHTML = minutes;
					document.getElementById('Tseconds').innerHTML = seconds;
					
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
