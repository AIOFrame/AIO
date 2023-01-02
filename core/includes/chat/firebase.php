<?php

class FIREBASE_CHAT {

    public function firebase_chat( int $chat_id, string $messages_list_class = '.messages_list', string $message_class = '.msg', string $input_class = '.message_content', string $send_message = '.send_message', string $message_meta = '.meta' ): void {
        $f = new FORM();
        ?>
        <div class="aio_firebase_chat" data-id="<?php echo $chat_id; ?>" data-message-class="<?php echo $message_class; ?>">
            <div class="messages_list <?php echo $messages_list_class; ?>">
                <div class="chat_set">
                    <div class="chats">
                        <div class="chat">
                            <div class="msg">Thank you, we will process it at earliest!</div>
                            <span class="time">5:01 PM</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="responder">
                <div class="responder">
                    <div class="row">
                        <div class="col-9">
                            <?php
                            $f->text('content','Message','Type your message...','','data-chat required');
                            $f->upload('file','Attachments','','',1,0,'','data-chat','jpg,jpeg,bmp,png,svg,pdf,psd,xd,ai,mov,mp4,avi,mp3,zip',10,1);
                            ?>
                        </div>
                        <div class="col-3 df jcc aic">
                            <div class="mat-ico cp mx-2" data-toggle-on=".aio_files">attach_file</div>
                            <?php $f->process_html('send','mat-ico','',''); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        get_styles('firebase-chat');
        get_scripts(['firebase-chat']);
    }

}