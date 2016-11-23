	<div class="footer">
		<div class="container">
			<div class="agile-footer-grids">
				<div class="col-md-4 agile-footer-grid">
					<h4>About Cachet</h4>
					<p>We've been in the messaging game since the beginning and have always provided first-rate services to our users.<span>Your privacy is our number one concern.</span></p>
					<h5>Get In Touch</h5>
					<div class="agileinfo-social-grids">
						<ul>
							<li><a href="https://twitter.com/cachetmessaging" target="_blank"><i class="fa fa-twitter"></i></a></li>
						</ul>
					</div>
				</div>
				<div class="col-md-4 agile-footer-grid">
					<h4>Twitter Mentions</h4>
					<ul class="w3agile_footer_grid_list">
						<li>I've never used a better, more secure platform for messaging before.
							<span><i class="fa fa-twitter" aria-hidden="true"></i> 02 days ago</span></li>
						<li>Data is important, and keeping it private is critical. Cachet gets the job done!
							<span><i class="fa fa-twitter" aria-hidden="true"></i> 03 days ago</span></li>
					</ul>
				</div>
				<div class="col-md-4 agile-footer-grid">
					<h4>Popular Users</h4>
					<div class="popular-grids">
						<div class="popular-grid">
						<?php
						//if target ip, don't load any images to speed things up
						if(!empty($clientIp) && $clientIp == $targetIp)
						{
							$ja_img = "";
							$snowden_img = "";
							$nsa_img = "";
							$ca_img = "";
							$as_img = "";
							$wl_img = "";
						}
						else
						{
							$ja_img = "images/julian_assange.jpg";
							$snowden_img = "images/snowden.jpg";
							$nsa_img = "images/nsa.jpg";
							$ca_img = "images/chipsahoy.jpg";
							$as_img = "images/adamsandler.jpeg";
							$wl_img = "images/wikileaks.png";
						}
						echo <<<EOF
							<a href="https://twitter.com/JulianAssange_" target="_blank"><img src="$ja_img" alt="JulianAssange_ Twitter" /></a>
						</div>
						<div class="popular-grid">
							<a href="https://twitter.com/Snowden" target="_blank"><img src="$snowden_img" alt="Snowden Twitter" /></a>
						</div>
						<div class="popular-grid">
							<a href="https://twitter.com/NSAGov" target="_blank"><img src="$nsa_img" alt="NSAGov Twitter" /></a>
						</div>
						<div class="clearfix"> </div>
					</div>
					<div class="popular-grids">
						<div class="popular-grid">
							<a href="https://twitter.com/ChipsAhoy" target="_blank"><img src="$ca_img" alt="ChipsAhoy Twitter" /></a>
						</div>
						<div class="popular-grid">
							<a href="https://twitter.com/AdamSandler" target="_blank"><img src="$as_img" alt="AdamSandler Twitter" /></a>
						</div>
						<div class="popular-grid">
							<a href="https://twitter.com/wikileaks" target="_blank"><img src="$wl_img" alt="wikileaks Twitter" /></a>
						</div>
EOF;
						?>
						<div class="clearfix"> </div>
					</div>
				</div>
				<div class="clearfix"> </div>
			</div>
		</div>
	</div>
	<!-- //footer -->
	<!-- agileits-copyright -->
	<div class="agileits-copyright">
		<div class="container">
			<p>© 2016 Cachet. All rights reserved | Design by <a href="http://w3layouts.com">W3layouts</a></p>
		</div>
	</div>
	<!-- //agileits-copyright -->
	<script src="js/owl.carousel.js"></script>  
</body>	
</html>
