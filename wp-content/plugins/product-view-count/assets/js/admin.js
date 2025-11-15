let pvc_modal = ( show = true ) => {
	if(show) {
		jQuery('#product-view-count-modal').show();
	}
	else {
		jQuery('#product-view-count-modal').hide();
	}
}

// Global function for resetting product count
function pvcResetProductCount(productId) {
	if (!confirm(pvcAdmin.strings.confirmReset || 'Are you sure you want to reset this product\'s view count?')) {
		return;
	}

	fetch(pvcAdmin.restUrl + 'analytics/reset-product', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-WP-Nonce': pvcAdmin.nonce
		},
		body: JSON.stringify({ product_id: productId })
	})
	.then(response => {
		if (!response.ok) {
			throw new Error('Network response was not ok');
		}
		return response.json();
	})
	.then(data => {
		console.log('Response data:', data);
		if (data.success) {
			alert(pvcAdmin.strings.resetSuccess || 'View count reset successfully!');
			location.reload();
		} else {
			console.error('Reset failed:', data);
			alert((pvcAdmin.strings.resetError || 'Error resetting view count: ') + (data.message || 'Unknown error'));
		}
	})
	.catch(error => {
		console.error('Error:', error);
		alert((pvcAdmin.strings.resetError || 'Error resetting view count: ') + error.message);
	});
}

jQuery(function($){
	$('.product-view-count-help-heading').click(function(e){
		var $this = $(this);
		var $target = $this.data('target');
		$('.product-view-count-help-text:not('+$target+')').slideUp();
		if($($target).is(':hidden')){
			$($target).slideDown();
		}
		else {
			$($target).slideUp();
		}
	});

	$('#product-view-count_report-copy').click(function(e) {
		e.preventDefault();
		$('#product-view-count_tools-report').select();

		try {
			var successful = document.execCommand('copy');
			if( successful ){
				$(this).html('<span class="dashicons dashicons-saved"></span>');
			}
		} catch (err) {
			console.log('Oops, unable to copy!');
		}
	});
})