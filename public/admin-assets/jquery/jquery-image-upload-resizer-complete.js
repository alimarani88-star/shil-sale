(function($) {
    $.fn.imageUploadResizer = function(options) {
        var settings = $.extend({
            max_width: 1000,
            max_height: 1000,
            quality: 0.9, // شروع کیفیت
            min_quality: 0.4, // کمترین کیفیت مجاز
            max_size_kb: 100,
            do_not_resize: [],
        }, options);

        this.filter('input[type="file"]').each(function () {
            this.onchange = function() {
                const that = this;
                const originalFile = this.files[0];

                if (!originalFile || !originalFile.type.startsWith('image')) return;

                if (settings.do_not_resize.includes('*') || settings.do_not_resize.includes(originalFile.type.split('/')[1])) {
                    return;
                }

                var reader = new FileReader();

                reader.onload = function (e) {
                    var img = new Image();
                    img.src = e.target.result;

                    img.onload = function () {
                        var canvas = document.createElement('canvas');
                        var ctx = canvas.getContext('2d');

                        const ratio = Math.min(settings.max_width / img.width, settings.max_height / img.height, 1);
                        const width = Math.round(img.width * ratio);
                        const height = Math.round(img.height * ratio);

                        canvas.width = width;
                        canvas.height = height;
                        ctx.drawImage(img, 0, 0, width, height);

                        function compressAndCheck(quality) {
                            canvas.toBlob(function(blob) {
                                if (blob.size / 1024 <= settings.max_size_kb || quality <= settings.min_quality) {
                                    const resizedFile = new File([blob], 'resized_' + originalFile.name, { type: 'image/jpeg' });

                                    const dataTransfer = new DataTransfer();
                                    dataTransfer.items.add(resizedFile);

                                    const currentOnChange = that.onchange;
                                    that.onchange = null;
                                    that.files = dataTransfer.files;
                                    that.onchange = currentOnChange;
                                } else {
                                    compressAndCheck(quality - 0.05); // کاهش کیفیت و تکرار
                                }
                            }, 'image/jpeg', quality);
                        }

                        compressAndCheck(settings.quality);
                    };
                };

                reader.readAsDataURL(originalFile);
            };
        });

        return this;
    };
})(jQuery);
