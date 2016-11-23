<?php
session_start();
$loggedIn = false;
if(isset($_SESSION['id']))
{
	$loggedIn = true;
	//header('Location: /unread.php');
	//die();
}

$activePage = "about";
require_once("header.php");
?>
		<div class="about-heading">
			<h2>About US</h2>
		</div>
	</div>
	<!-- //banner -->
	
	<!-- about-top -->
	<div class="agile-about-top">
		<div class="container">
			<div class="about-section">
				<div class="col-md-7 ab-left">
				  <div class="grid">
			        <div class="h-f">
					<figure class="effect-jazz">
					<img src="images/Sargon_of_Akkad.jpg" alt="Our Founder, Sargon of Akkad">
						<figcaption>
							<h4>Our <span>Founder</span></h4>
							<p>The vacancy of privacy castrates the human dexterity for serenity</p>
						</figcaption>			
					</figure>
					
				 </div>
				 <div class="h-f">
					<figure class="effect-jazz">
						<img src="images/privacy.jpg" alt="img25">
						<figcaption>
							<h4 style="padding-top: 23%;">Our <span>Purpose</span></h4>
							<p>Forge a world where privacy is valued above all else</p>
						</figcaption>			
					</figure>
					
				 </div>
				 <div class="clearfix"> </div>
				 </div>
			   </div>
			   <div class="col-md-5 ab-text">
			         <h3>A brief history of our company</h3>
					<p>It all began when written language became popular. Our founder, Sargon of Akkad, knew instantly that for the rest of eternity, people would want to keep and exchange written secrets.
					<span>Sargon's vision was instrumental in the success of our company, but now we are in the hands of our CEO Walter Blanco who will lead our company to continued success and greatness.</span></p>
				</div>
				<div class="clearfix"> </div>
			 </div>
		</div>
	</div>
	<!-- //about-top -->
	
	<!-- team -->
	<div class="jarallax team">
		<div class="container">
			<div class="team-heading">
				<h3>Our Team</h3>
				<p>Our current leadership team, guiding our company into the Next Generation.</p>
			</div>
			<div class="agileits-team-grids">
				<div class="col-md-3 agileits-team-grid">
					<div class="team-info">
						<img src="images/ceo.jpg" style="height: 381px;" alt="CEO" />
						<div class="team-caption"> 
							<h4>Walter Blanco</h4>
							<p>CEO</p>
							<ul>
								<li><a target="_blank" href="https://twitter.com/cachetmessaging"><i class="fa fa-facebook"></i></a></li>
								<li><a target="_blank" href="https://twitter.com/cachetmessaging"><i class="fa fa-twitter"></i></a></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-md-3 agileits-team-grid">
					<div class="team-info">
						<img src="images/t2.jpg" alt="Chief Paranoia Evangelist" />
						<div class="team-caption"> 
							<h4>Elliot Alderson</h4>
							<p>Chief Paranoia Evangelist</p>
							<ul>
								<li><a target="_blank" href="https://twitter.com/cachetmessaging"><i class="fa fa-facebook"></i></a></li>
								<li><a target="_blank" href="https://twitter.com/cachetmessaging"><i class="fa fa-twitter"></i></a></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-md-3 agileits-team-grid">
					<div class="team-info">
						<img src="images/costner.jpg" style="height: 381px;" alt="Chief Privacy Officer" />
						<div class="team-caption"> 
							<h4>Kevin Costner</h4>
							<p>Chief Privacy Officer</p>
							<ul>
								<li><a target="_blank" href="https://twitter.com/cachetmessaging"><i class="fa fa-facebook"></i></a></li>
								<li><a target="_blank" href="https://twitter.com/cachetmessaging"><i class="fa fa-twitter"></i></a></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-md-3 agileits-team-grid">
					<div class="team-info">
						<img src="images/t4.jpg" alt="Minister of Cryptography" />
						<div class="team-caption"> 
							<h4>Jeff</h4>
							<p>Minister of Cryptography</p>
							<ul>
								<li><a target="_blank" href="https://twitter.com/cachetmessaging"><i class="fa fa-facebook"></i></a></li>
								<li><a target="_blank" href="https://twitter.com/cachetmessaging"><i class="fa fa-twitter"></i></a></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="clearfix"> </div>
			</div>
		</div>
	</div>
	<!-- //team -->
	
<?php
require_once("footer.php");
?>
