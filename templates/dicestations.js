$(document).ready(function() {
	let mb = 0;
	
	let sw = $('.xcode_stations').width();
	
	let isMobile = window.matchMedia("only screen and (max-width: 760px)");
	
	let ae = function() {
		$('.xcode_station').mouseenter(
				function() {
					$('.xcode_station_text', this).css('display', 'block');
					$('a', this).css('z-index', '2001');
				});
		$('.xcode_station').mouseleave(
				function() {
					$('.xcode_station_text', this).css('display', 'none');
					$('a', this).css('z-index', 'initial');
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
			while (ew < 200) {
				ln++;
				el = Math.ceil(s.length/ln);
				ew = sw/el;
			}
		}
		
		if (el > 1) {
			$('.xcode_station').attr('style', '');
			$('.xcode_station_text').attr('style', '');
			
			if (mb == 1) {
				ae();
				$('.xcode_stations').removeClass('xcode_mobile');
				mb = 0;
			}
			
			let h = 0;		
			s.each(
				function(i, v) {
					if (Math.ceil((i+1)/el)%2 == 1) {
						left = (i%el)*ew-ew/4+(ew-(ew/2))/2;
					} else {
						left = (i%el)*ew-ew/4+(ew+(ew/2))/2;
					}
					
					$(v).css('left', left);
					let to = Math.floor((i)/el)*128;
					$(v).css('top', 
							to);
					if (to+135+$('.xcode_station_text', v).height() > h) {
						h = to+135+$('.xcode_station_text', v).height();
					}	
				});
			$('.xcode_stations').height(h);
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
