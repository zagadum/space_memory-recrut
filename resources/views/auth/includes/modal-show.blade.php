 
            <div id="myModal" class="auth modal"  style="display: {{ $showSetLocationModal ? 'flex' : 'none' }}"> 
                <div class="modal-content">
                    <center><p><strong class="title">{{$ipService->aText["question_modal_window_1"]}}</strong></p></center>
                    <center><p>{{$ipService->aText["question_modal_window_2"]}}</p></center>
                    <div style="display: flex; justify-content: center; gap: 10px;">
                        <button class="stay {{$ipService->urlDomain}}"    onclick="closeModal(0,'');">
                            {{
                                $ipService->aText[
                                    $ipService->urlDomain == 'pl' ? 'stayon_button_pl' :
                                    ($ipService->urlDomain == 'ua' ? 'stayon_button_ua' :
                                    ($ipService->urlDomain == 'en' ? 'stayon_button_en' : 'stayon_button'))
                                ]
                            }}
                        </button>
                        <button class="{{$ipService->ipDomain}}" onclick="closeModal(1,'{{$ipService->change_domain}}');">{{$ipService->aText["change_platform_button"]}}</button>
                    </div>
                </div>
            </div>

            <script type="text/javascript">
                
                function closeModal(go,subdomain) {
                    var main_domain = '{{$main_domain}}';
                    console.log("subdomain",main_domain);
                    if (go)  window.location.href =   window.location.protocol + "//" + subdomain
                    else{
                        let expires = new Date();
                        expires.setTime(expires.getTime() + 30*24*60*60*1000); 
                        document.cookie = "setLocation=" + encodeURIComponent(window.location.hostname) +
                                        "; expires=" + expires.toUTCString() +
                                        "; path=/" +
                                        "; domain=." + main_domain;
                        document.getElementById("myModal").style.display = "none";
                        window.location.reload();
                    }
                }
            </script>