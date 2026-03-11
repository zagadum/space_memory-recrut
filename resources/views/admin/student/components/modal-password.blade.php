<modal-changepassword-form v-cloak inline-template >
    <modal class="admin-modal" name="ChangePasswordModal" transition="pop-out"  :focus-trap="false" @before-open="beforeOpen">

        <button type="button" class="btn-close" @click="clicktoclose"><img src="{{asset('images/x.svg')}}"></button>
        <div class="box">

            <p class="box-message">Новый пароль будет отправлен пользователю на почту.</p>

            <div class="box-btn">
                <button class="box-btn_primary"  @click="PressOK" >Сменить</button>
            </div>

        </div>
    </modal>
</modal-changepassword-form>
