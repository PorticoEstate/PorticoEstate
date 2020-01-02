$(document).ready(function ()
{
	document.getElementById('file_input').onchange = function ()
	{
		$('.files_to_upload').remove();

		var file;
		var file_size;
		if (this.files.length > 1)
		{
			for (var i = 0; i < this.files.length; i++)
			{
				file = this.files[i];
				file_size = formatFileSize(file.size);
				$('<div class="files_to_upload">File: ' + file.name + ' size: ' + file_size + '</div>').insertAfter(this);
			}
		}
	};


//https://www.smashingmagazine.com/2018/01/drag-drop-file-uploader-vanilla-js/
//chunking
//https://gist.github.com/shiawuen/1534477
// ************************ Drag and drop ***************** //
	let dropArea = document.getElementById("drop-area")
	let fileInput = document.getElementById('file_input');
// Prevent default drag behaviors
		;
	['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
		dropArea.addEventListener(eventName, preventDefaults, false)
		document.body.addEventListener(eventName, preventDefaults, false)
	})

// Highlight drop area when item is dragged over it
		;
	['dragenter', 'dragover'].forEach(eventName => {
		dropArea.addEventListener(eventName, highlight, false)
	})

		;
	['dragleave', 'drop'].forEach(eventName => {
		dropArea.addEventListener(eventName, unhighlight, false)
	})

// Handle dropped files
	dropArea.addEventListener('drop', handleDrop, false)

	function preventDefaults(e)
	{
		e.preventDefault()
		e.stopPropagation()
	}

	function highlight(e)
	{
		dropArea.classList.add('highlight')
	}

	function unhighlight(e)
	{
		dropArea.classList.remove('active')
	}

	function handleDrop(e)
	{
		var dt = e.dataTransfer
		var files = dt.files

		handleFiles(files)
	}

	function handleFiles(files)
	{
		fileInput.files = files;

		files = [...files]

		$('.files_to_upload').remove();

		var file;
		var file_size;
		if (files.length > 1)
		{
			for (var i = 0; i < files.length; i++)
			{
				file = files[i];
				file_size = formatFileSize(file.size);

				$('<div class="files_to_upload">File: ' + file.name + ' size: ' + file_size + '</div>').insertAfter(fileInput);
			}
		}
	}

	formatFileSize = function (bytes)
	{
		if (typeof bytes !== 'number')
		{
			return '';
		}
		if (bytes >= 1000000000)
		{
			return (bytes / 1000000000).toFixed(2) + ' GB';
		}
		if (bytes >= 1000000)
		{
			return (bytes / 1000000).toFixed(2) + ' MB';
		}
		return (bytes / 1000).toFixed(2) + ' KB';
	};


});
