$(document).ready(function() {
	let mb = 0;
	
	let sw = $('.xcode_stations').width();
	
	let isMobile = window.matchMedia("only screen and (max-width: 760px)");
	
	let ae = function() {
		$('.xcode_station').mouseenter(
				function() {
					$('.xcode_station_text', this).css('display', 'block');
				});
		$('.xcode_station').mouseleave(
				function() {
					$('.xcode_station_text', this).css('display', 'none');
				});
	}
	
	let re = function() {
		$('.xcode_station').off();
	}
	
	let dist = function() {
		let s = $('.xcode_station');
		let ln = 1;
		let ew = sw/s.length;
		let el = s.length;
		if (isMobile.matches) {
			el = 1;
		} else {
			while (ew < 150) {
				ln++;
				el = Math.ceil(s.length/ln);
				ew = sw/el;
			}
		}
		
		if (el > 1) {
			$('.xcode_stations').height(ln*92);
			$('.xcode_station').attr('style', '');
			$('.xcode_station_text').attr('style', '');
			
			if (mb == 1) {
				ae();
				$('.xcode_stations').removeClass('xcode_mobile');
				mb = 0;
			}
					
			s.each(
				function(i, v) {
					if (Math.ceil((i+1)/el)%2 == 1) {
						left = (i%el)*ew-ew/4+(ew-(ew/2))/2;
					} else {
						left = (i%el)*ew-ew/4+(ew+(ew/2))/2;
					}
					
					$(v).css('left', left);
					$(v).css('top', 
							Math.floor((i)/el)*80);
					let offset = $(v).offset().left;
					if (offset < 63) {
						$('.xcode_station_text', this).css('left', -(offset/2) + 'px');
					} else if ((corr = $(window).width() - offset - 64) < 63) {
						$('.xcode_station_text', this).css('left', -136 + corr/2 +'px');
					}
				});
		} else if (el == 1 && mb == 0) {
			mb = 1;
			re();
			$('.xcode_station').attr('style', '');
			$('.xcode_stations').attr('style', '');
			$('.xcode_station_text').attr('style', '');
			$('.xcode_stations').addClass('xcode_mobile');
		}
	}
	
	ae();
	dist();
	
	$(window).resize(
			function() {
				if ( sw != $('.xcode_stations').width()) {
					sw = $('.xcode_stations').width();
					dist();
				}
			});
})