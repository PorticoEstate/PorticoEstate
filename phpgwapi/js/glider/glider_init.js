var glider;

window.addEventListener('load', function ()
{
	try
	{

		glider = new Glider(document.querySelector('.glider'), {
			slidesToShow: 1,
			draggable: true,
			arrows: {
				prev: '.glider-prev',
				next: '.glider-next'
			},
			easing: function (x, t, b, c, d)
			{
				return c * (t /= d) * t + b;
			},
			dots: '.dots'
		});

		document.querySelector('.glider').addEventListener('glider-slide-visible', function (event)
		{
			var imgs_to_anticipate = 1;
			var glider = Glider(this);
			for (var i = 0; i <= imgs_to_anticipate; ++i)
			{
				var index = Math.min(event.detail.slide + i, glider.slides.length - 1),
					glider = glider;
				loadImages.call(glider.slides[index], function ()
				{
					glider.refresh(true);
				})
			}
		});

		if (glider.slides.length > 0)
		{
			$('.wrapperForGlider').show();
			glider.refresh(true);
			loadImages.call(glider.slides[0]);
		}

	}
	catch (e)
	{

	}

});



function loadImages(callback)
{
	[].forEach.call(this.querySelectorAll('img'), function (img)
	{
		var _img = new Image, _src = img.getAttribute('data-src');
		_img.onload = function ()
		{
			img.src = _src;
			img.classList.add('loaded');
			callback && callback(img);
		}
		if (img.src !== _src)
		{
			_img.src = _src;
		}
	});
}


this.refresh_glider = function (strURL)
{
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: strURL,
		success: function (data)
		{
			if (data != null)
			{
				var slides = glider.slides.length - 1;
				for (i = slides; i >= 0; i--)
				{
					glider.removeItem(i);
				}

				var files = data.data;
				$.each(files, function (k, v)
				{
					if (typeof (v.img_url) !== 'undefined' && v.img_url)
					{
						var div = document.createElement('div');
						var img = document.createElement('img');
						img.setAttribute('data-src', v.img_url.replace(/\&amp;/g, '&'));
						img.alt = v.file_name;
						div.appendChild(img);
						glider.addItem(div);
					}
				});
				if (glider.slides.length > 0)
				{
					$('.wrapperForGlider').show();
					glider.refresh(true);
					loadImages.call(glider.slides[0]);
				}
				else
				{
					$('.wrapperForGlider').hide();
				}
			}
		}
	});
};
