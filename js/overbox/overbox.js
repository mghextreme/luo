(function($) {
	$boxKeysShortcuts = function(e){
		switch (e.keyCode) {
			case 13: //Enter
				e.preventDefault();
				$box.confirm();
				break;
			case 27: //Escape
				e.preventDefault();
				$box.close();
				break;
		}
	}
	
	$.Box = function() { };
	
	$.Box.prototype = {
		setBackground: function(opac, col) {
	    	var opacity = arguments.length > 0 ? opac : 0.8,
	    		color = arguments.length > 1 ? col : 'black',
	    		that = this;
	    	
			$('<div>')
			.attr({ id: 'box-background' })
			.css({ opacity: opacity, backgroundColor: color, display: 'none' })
			.click(function(){ that.close(); })
			.appendTo('body')
			.fadeIn(300);
		},
		getWidth: function(){
			var maxWidth = $(window).width(),
				width = 500;
			if (maxWidth < width)
			{ width = maxWidth; }
			return width;
		},
		isOpen: function(){
			return $('body').children('div#box-background').size() > 0;
		},
	    alert: function(mens, btn, act) {
	    	if (this.isOpen()){ return; }
	    	
	    	var message = arguments.length > 0 ? mens : 'Mensagem padrão.',
	    		func = (arguments.length == 2 && typeof(btn) == "function"),
	    		button = arguments.length > 1 && !func ? btn : 'Continuar',
	    		that = this,
	    		action = arguments.length > 2 ? act : (func ? btn : that.close);
	    	this.setBackground(0.7);
	    	
	    	this.confirmAction = action;
	    	
	    	var $main = $('<div>')
	    	.attr({ id: 'box-main' })
			.css({ width: this.getWidth(), display: 'none' })
			.append(
	    		$('<div>')
	    		.attr({ id: 'box-message' })
	    		.html(message)
	    	)
	    	.append(
	    		$('<div>')
	    		.attr({ id: 'box-buttons' })
	    		.append(
	    			$('<div>')
	    			.attr({ id: 'box-button-exit' })
					.css({ width: Math.floor(this.getWidth() / 2) - 20 })
	    			.text(button)
	    			.click(function(){ that.confirm(); })
	    		)
			)
	    	.appendTo('body');
	    	
	    	$main.fadeIn(300);
	    	
	    	setTimeout(function(){ $('html').on('keydown', $boxKeysShortcuts); }, 300);
	    },
	    ask: function(mens, btnY, btnN, act) {
	    	if (this.isOpen()){ return; }
	    	
	    	var message = arguments.length > 0 ? mens : 'Mensagem padrão.',
	    		func = (arguments.length == 2 && typeof(btnY) == "function"),
	    		buttonYes = arguments.length > 1 && !func ? btnY : 'Continuar',
	    		buttonNo = arguments.length > 2 && !func? btnN : 'Cancelar',
	    		that = this,
	    		action = arguments.length > 3 ? act : (func ? btnY : that.close);
	    	this.setBackground(0.7);
	    	
	    	this.confirmAction = action;
	    	
	    	var $main = $('<div>')
	    	.attr({ id: 'box-main' })
			.css({ width: this.getWidth(), display: 'none' })
			.append(
	    		$('<div>')
	    		.attr({ id: 'box-message' })
	    		.html(message)
	    	)
	    	.append(
	    		$('<div>')
	    		.attr({ id: 'box-buttons' })
	    		.append(
	    			$('<div>')
	    			.attr({ id: 'box-button-submit' })
					.css({ width: Math.floor(this.getWidth() / 2) - 15 })
	    			.text(buttonYes)
	    			.click(function(){ that.confirm(); })
	    		)
	    		.append(
	    			$('<div>')
	    			.attr({ id: 'box-button-exit' })
					.css({ width: Math.floor(this.getWidth() / 2) - 15 })
	    			.text(buttonNo)
	    			.click(function(){ that.close(); })
	    		)
			)
	    	.appendTo('body');
	    	
	    	$main.fadeIn(300);
	    	
	    	setTimeout(function(){ $('html').on('keydown', $boxKeysShortcuts); }, 300);
	    },
	    confirm: function() {
	    	this.confirmAction();
	    	this.close();
	    },
	    close: function() {
	    	this.confirmAction = function(){  };
	    	$('div#box-background').fadeOut(300, function(){ $(this).remove(); });
	    	$('div#box-main').fadeOut(300, function(){ $(this).remove(); });
	    	
	    	setTimeout(function(){ $('html').on('keydown', $boxKeysShortcuts); }, 300);
	    }
	};
	
	$.Box.defaultOptions = {
		confirmAction: function() {  }
	};
}(jQuery));

$box = new $.Box();