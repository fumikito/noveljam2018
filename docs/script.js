/**
 * Analytics File
 */

/*global hoge: true*/


window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'UA-5329295-4');

(function ($) {

  'use strict';

  var start = new Date();
  var read = false;

  $(document).ready(function(){

    var $main = $('.main');
    var width = $main.scrollWidth;
    $main.scroll(function(e){
      if(read){
        return;
      }
      var size = Math.abs($main.scrollLeft()) + $main.width();
      var offset = this.scrollWidth - 40;
      if ( Math.abs(offset - size) < 100 ) {
        read = true;
        setTimeout(function(){
          $('body').addClass('read');
        }, 5000);
      }
    });

    $('.button').click(function(){
      var label = $(this).attr('data-action');
      var value  = parseInt( $(this).attr("data-value"), 10 );
      var url = 'https://bccks.jp/bcck/153422/info';
      gtag('event', 'read', {
        event_category: 'engagement',
        event_label: label,
        event_value: value,
        event_callback: function() {
          if( window.confirm( 'ご回答ありがとうございました！ ストアに移動しますか？' ) ) {
            window.location.href = url;
          } else {
            window.prompt( 'SNSなどでこのURLをシェアしてください。', url );
          }
          $('body').removeClass('read');
        }
      });
    });
  })


})(jQuery);
