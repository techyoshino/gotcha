


jQuery(function($){

	var show = 2; //最初に表示する件数
	var num = 2;  //clickごとに表示したい件数
	var contents = '.container---in'; // 対象のlist
	$(contents + ':nth-child(n + ' + (show + 1) + ')').addClass('is-hidden');
	$('.more').on('click', function () {
	$(contents + '.is-hidden').slice(0, num).removeClass('is-hidden');
	if ($(contents + '.is-hidden').length == 0) {
		$('.more').fadeOut();
	}
	});
});