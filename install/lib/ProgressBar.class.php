<?php

/**
 * Progress bar for a lengthy PHP process
 * http://spidgorny.blogspot.com/2012/02/progress-bar-for-lengthy-php-process.html
 */
class ProgressBar {

    var $percentDone = 0;
    var $pbid;
    var $pbarid;
    var $tbarid;
    var $textid;
    var $decimals = 1;
    var $showTimes = 1;
    var $hideOnComplete = 0;

    function __construct($percentDone = 0) {
        $this->pbid = 'pb';
        $this->pbarid = 'progress-bar';
        $this->tbarid = 'transparent-bar';
        $this->textid = 'pb_text';
        $this->percentDone = $percentDone;
    }

    function render() {
        //print ($GLOBALS['CONTENT']);
        //$GLOBALS['CONTENT'] = '';
        print($this->getContent());
        $this->flush();
        //$this->setProgressBarProgress(0);
    }

    function getContent() {
        $this->percentDone = floatval($this->percentDone);
        $percentDone = number_format($this->percentDone, $this->decimals, '.', '') . '%';
        $content .= '<div id="' . $this->pbid . '" class="pb_container">
                        <div id="' . $this->textid . '" class="' . $this->textid . '">' . $percentDone . '</div>
                        <div class="pb_bar">
                                <div id="' . $this->pbarid . '" class="pb_before"
                                style="width: ' . $percentDone . ';"></div>
                                <div id="' . $this->tbarid . '" class="pb_after"></div>
                        </div>
                </div>
                <style>
                        .pb_container {
                                position: relative;
                        }
                        .pb_bar {
                                width: 100%;
                                height: 1.3em;
                                border: 1px solid silver;
                                -moz-border-radius-topleft: 5px;
                                -moz-border-radius-topright: 5px;
                                -moz-border-radius-bottomleft: 5px;
                                -moz-border-radius-bottomright: 5px;
                                -webkit-border-top-left-radius: 5px;
                                -webkit-border-top-right-radius: 5px;
                                -webkit-border-bottom-left-radius: 5px;
                                -webkit-border-bottom-right-radius: 5px;
                        }
                        .pb_before {
                                float: left;
                                height: 1.3em;
                                background-color: #43b6df;
                                -moz-border-radius-topleft: 5px;
                                -moz-border-radius-bottomleft: 5px;
                                -webkit-border-top-left-radius: 5px;
                                -webkit-border-bottom-left-radius: 5px;
                        }
                        .pb_after {
                                float: left;
                                background-color: #FEFEFE;
                                -moz-border-radius-topright: 5px;
                                -moz-border-radius-bottomright: 5px;
                                -webkit-border-top-right-radius: 5px;
                                -webkit-border-bottom-right-radius: 5px;
                        }
                        .pb_text {
                                padding-top: 0.1em;
                                position: absolute;
                                left: 48%;
                        }
			.clear {
				height: 1px;
				font-size: 1px;
				clear:both;
			}
			#elapsed {
				float:left;
			}
			#remaining {
				float:right;
			}
                        .alert {
                          text-shadow: 0 1px 0 rgba(255, 255, 255, 0.2);
                          -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25), 0 1px 2px rgba(0, 0, 0, 0.05);
                                  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25), 0 1px 2px rgba(0, 0, 0, 0.05);
                                  padding: 10px;
                        }
                        .alert-success {
                          background-image: -webkit-linear-gradient(top, #dff0d8 0%, #c8e5bc 100%);
                          background-image: linear-gradient(to bottom, #dff0d8 0%, #c8e5bc 100%);
                          background-repeat: repeat-x;
                          border-color: #b2dba1;
                          filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#ffdff0d8, endColorstr=#ffc8e5bc, GradientType=0);
                        }
                        .alert-info {
                          background-image: -webkit-linear-gradient(top, #d9edf7 0%, #b9def0 100%);
                          background-image: linear-gradient(to bottom, #d9edf7 0%, #b9def0 100%);
                          background-repeat: repeat-x;
                          border-color: #9acfea;
                          filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#ffd9edf7, endColorstr=#ffb9def0, GradientType=0);
                        }
                        .alert-warning {
                          background-image: -webkit-linear-gradient(top, #fcf8e3 0%, #f8efc0 100%);
                          background-image: linear-gradient(to bottom, #fcf8e3 0%, #f8efc0 100%);
                          background-repeat: repeat-x;
                          border-color: #f5e79e;
                          filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#fffcf8e3, endColorstr=#fff8efc0, GradientType=0);
                        }
                        .alert-danger {
                          background-image: -webkit-linear-gradient(top, #f2dede 0%, #e7c3c3 100%);
                          background-image: linear-gradient(to bottom, #f2dede 0%, #e7c3c3 100%);
                          background-repeat: repeat-x;
                          border-color: #dca7a7;
                          filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#fff2dede, endColorstr=#ffe7c3c3, GradientType=0);
                        }
                        p {margin: 0 0 10px;}
                </style>' . "\r\n";
        if ($this->showTimes) {
            $content .= '<div id="elapsed">0:00</div><div id="remaining"></div>
			<script type="text/javascript">
				function pad(n){n=parseInt(n);return n<10?"0"+n:n;}
				function setRemaining(elapsed){
					var percent = parseFloat(document.getElementById("' . $this->textid . '").innerHTML);
					var remaining = (elapsed*100/(percent || 1))-elapsed;
					var minutes = parseInt(remaining/60);
					var seconds = remaining%60;
					document.getElementById("remaining").innerHTML = minutes+":"+pad(seconds);
				}
				function setElapsed(){
					var elapsed = document.getElementById("elapsed").innerHTML.split(":");
					var minutes = parseInt(elapsed[0]);
					var seconds = parseInt(elapsed[1])+1;
					if(seconds>=60){minutes++;seconds=0;}
					document.getElementById("elapsed").innerHTML = minutes+":"+pad(seconds);
					setRemaining(minutes*60+seconds);
				}
				var addElapsed = setInterval(setElapsed, 1000);
			</script><br class="clear" />' . "\r\n";
        }
        return $content;
    }

    function setProgressBarProgress($percentDone, $text = '') {
        $this->percentDone = $percentDone;
        $text = $text ? $text : number_format($this->percentDone, $this->decimals, '.', '') . '%';
        print('
                <script type="text/javascript">
                if (document.getElementById("' . $this->pbarid . '")) {
			document.getElementById("' . $this->pbarid . '").style.width = "' . $percentDone . '%";}');
        if ($percentDone == 100) {
            if ($this->showTimes) {
                print('document.getElementById("remaining").innerHTML = "0:00";clearInterval(addElapsed);');
            }
            if ($this->hideOnComplete) {
                print('document.getElementById("' . $this->pbid . '").style.display = "none";');
            }
        } else {
            print('document.getElementById("' . $this->tbarid . '").style.width = "' . (100 - $percentDone) . '%";');
        }
        if ($text) {
            print('document.getElementById("' . $this->textid . '").innerHTML = "' . htmlspecialchars($text) . '";');
        }
        print('</script>' . "\n");
        $this->flush();
    }

    function flush() {
        print str_pad('', intval(ini_get('output_buffering'))) . "\n";
        //ob_end_flush();
        flush();
    }

}

