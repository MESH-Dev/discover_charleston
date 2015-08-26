<div class="clearfix"></div>
</div><!-- content --> 


<!-- footer -->

<footer class="footer-block <?php if ( is_front_page() ) {	echo'home-footer'; } ?>">

<div class="col-3-layout container footer-imp-panels">

<!-- col -->

<div class="col bgcol1">


<div class="colwrap">
	<h4>IS YOUR EVENT NOT LISTED ON OUR CALENDAR?</h4>
<p> <a href="<?php echo get_permalink(1398); ?>" style="color: white"><center>SUBMIT EVENTS HERE </center></a></p>
</div>

</div><!-- col -->


<!-- col --><div class="col bgcol2 wheather-col"><div class="colwrap">

	<h4><?php echo get_field('event_title'); ?></h4><!-- WEATHER / TEMPERATURE FEED  -->
	<p style="text-align:center; text-transform:uppercase;"><a href="<?php echo get_field('link'); ?>"><?php echo get_field('event_description'); ?></a></p>


<?php//echo do_shortcode('[wpc-weather id="22"]'); ?>
	</div>
</div><!-- col -->



<!-- col --><div class="col bgcol5">
<div class="colwrap">
	<h4>Sign up for discover charleston news</h4>
	<p style="text-align:center; font-size: 14px;"><form action="http://visitor.r20.constantcontact.com/d.jsp?llr=c8kehfcab&p=oi&m=1101834365794&sit=sb5mqyvcb&f=b4c5a0b3-1361-41f5-b4d7-f95a4e334890" method="post" class="ft-form"><input type="text" class="mar-r-5"><button>Sign Up</button></form></p>
	</div><!-- col -->
	</div>
</div>

<div class="clearfix"></div>

<div class="row">
<div class="container2">
	<div class="container2-content">

<div class="f1-block"><a href="<?php echo get_site_url(); ?>" class="monoarch-footer"><img src="<?php echo get_bloginfo('template_url') ?>/images/monarch-footer.png" /></a>FAMILY OF HOTELS</div>

<div class="f2-block">
<h3>Hampton Inn Southridge</h3>
<p>1 Preferred Place<br>
Charleston, WV 25309<br>
304-746-4646</p>
<a href="http://hamptoninn3.hilton.com/en/hotels/west-virginia/hampton-inn-charleston-southridge-CRWSOHX/index.html">Book Now  &raquo;</a>
<a href="http://monarchhotelswv.com/" class="bottom-logos lg1"></a>
</div>

<div class="f3-block">
<h3>Holiday Inn &amp; Suites Charleston-West</h3>
<p>400 2nd Ave., SW<br>
Charleston, WV 25303<br>
304-744-4641</p>
<a href="http://www.ihg.com/holidayinn/hotels/us/en/charleston/crwch/hoteldetail?qAdlt=1&qBrs=6c.hi.ex.rs.ic.cp.in.sb.cw.cv.ul.vn&qChld=0&qFRA=1&qGRM=0&qPSt=0&qRRSrt=rt&qRef=df&qRms=1&qRpn=1&qRpp=12&qSHp=1&qSmP=3&qSrt=sBR&qWch=0&srb_u=1&icdv=99502222&icdv=99502222">Book Now  &raquo;</a>
<a href="http://hicharleston.com/" class="bottom-logos lg2"></a>
</div>

<div class="f4-block">
<h3>Wingate by Wyndham Charleston</h3>
<p>402 2nd Ave., SW<br>
Charleston, WV 25303<br>
304-744-4444</p>
<a href="http://www.wingatehotels.com/hotels/west-virginia/charleston/wingate-by-wyndham-charleston-wv/rooms-rates?checkin_date=06/29&checkout_date=06/30&rooms=1&adults=1&children=0&ratePlan=BAR&force_nostay=false&hotel_id=10115&brand_id=WG&intcmp=16878&corporate_id=1000002810&ref_id=http://www.wyndham.com">Book Now &raquo;</a>
<a href="http://wingatecharleston.com/" class="bottom-logos lg3"></a>
</div>


<div class="f5-block">
<h3>Charleston Conference<br /> Center</h3>
<p>400 2nd Ave., SW<br>
Charleston, WV 25303<br>
304-744-4641</p>

<a href="">Book Your Event Here Now &raquo;</a>
</div>
</div>
</div>
</div>




<div class="clearfix"></div>

</footer><!-- footer --> 

 
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5581f227307a0327" async="async"></script>


<script>
jQuery( document ).ready(function() {

	jQuery('span.at4-icon').css('background-size','38px !important'); 

});

</script>
<?php wp_footer(); ?>

</body>
</html>