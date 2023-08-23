<?php
 
    //VARIABILI
    $token = "";
    $data = file_get_contents("php://input");
    /*Decodifica il json*/
    $update = json_decode($data, true);
 
    $message = $update["message"];
    $from = $message["from"];
 
    $text = $message["text"];
    $userID = $from["id"];
    $ChatID = $message["chat"]["id"];
    $username = $from["username"];
    $name = $from["first_name"];
    $cognome = $form["last_name"];
    $ReplyMsgID = $message['reply_to_message']['message_id'];
 
    $MsgID = $message["message_id"];
    $FromChatID = $message["forward_from"]["id"];
 
    $photoID = $update["message"]["photo"][0]["file_id"];
    $textPhoto = $update["message"]["caption"];
 
    $query = $update["callback_query"];
    $queryFrom = $query["from"];
    $queryUsername = $queryFrom["username"];
    $queryChatID = $update['callback_query']['message']['chat']['id'];
    $queryName = $queryFrom["first_name"];
    $queryID = $query["id"];
    $queryUserID = $queryFrom["id"];
    $queryData = $query["data"];
    $queryMsgID = $query["message"]["message_id"];
 
    $enter = true;
 
    //TASTIERE
    $Menu = [
                   [
                      [
                        'text' => "✉️ Status chat ✉️",
                        'callback_data' => "Menu",
                      ],
                   ],
                   [
                     [
                       'text' => "👨🏼‍💻 Info 👨🏼‍💻",
                       'callback_data' => "info",
                     ],
                   ],
              ];
    $Back = [
                [
                    [
                        'text' => "<<    Torna indietro   <<",
                        'callback_data' => "retry",
                    ],
                ],
            ];
 
    //COMANDI
 
    if(isset($text))
    {
        switch($text)
        {
            case "/start":
                $enter = false;
 
                sendMessage($ChatID,"Benvenuto <a href='tg://user?id=$userID'>$name</a> >> [<code>$userID</code>]\n\nQuesto è il bot per contattare @The_Luke nel caso tu fossi limitato. Non devi digitare nessun comando, ti basta scrivere ed inviare ciò di cui hai bisogno!\n\nControlla tramite il bottone qui sotto se la chat è libera o meno! (Ti metterà in automatico in lista d'attesa).",$Menu,"inline");
            break;
 
            case "/finish":
                $enter = false;
 
                if($ChatID == 83356786)
                {
                    $Check = file_get_contents("chat.txt");
                    if($Check == "on")
                    {
                        $mintime = file_get_contents("time2.txt");
                        $maxtime = time();
                        $U_Time = $maxtime - $mintime;
 
                        file_put_contents("chat.txt","off");
 
                        $Busy = file_get_contents("user.txt");
                        $ChatEND = file_get_contents("message.txt");
                        $ID = explode(" ",$ChatEND);
 
                        sendMessage($ChatID,"Chat con <a href='tg://user?id=$ID[0]'>$Busy</a> chiusa.\n\nDurata: $U_Time Secondi.");
                        sendMessage($ChatID,"Ho riaperto il bot a tutti.");
 
                        file_put_contents("time.txt",time());
                        sendMessage($ID[0],"La tua chat è appena stata chiusa.\nDurata: $U_Time Secondi.\n\n/start >> Riavvia il bot.");
 
                        file_put_contents("message.txt","");
                        $file = file("attend.txt");
                        $i = 0;
                        foreach($file as $i => $raw)
                        {
                            sendMessage($raw,"La chat con @The_Luke si è liberata, affreattati a scrivere un messaggio prima che lo facciano prima di te!");
                        }
                        file_put_contents("attend.txt","");
                    }
                    else
                        {
                            sendMessage($ChatID,"Errore, impossibile chiudere la chat.",$Menu,"inline");
                        }
                }
            break;
        }
    }
 
    if((isset($message) && ($enter == true)))
    {
        $Check = file_get_contents("chat.txt");
 
        $To_Reply = file_get_contents("message.txt");
        $ID = explode(" ",$To_Reply);
 
 
        $mintime = file_get_contents("time.txt");
        $maxtime = time();
        $Wait_Time = $maxtime - $mintime;
 
        if(($Check == "off") && ($ChatID != 83356786) && ($Wait_Time > 15))
        {
            file_put_contents("chat.txt","on");
            $Check = "on";
            file_put_contents("message.txt","$ChatID $MsgID");
 
            $To_Reply = file_get_contents("message.txt");
            $ID = explode(" ",$To_Reply);
 
            sendMessage(83356786,"[<a href='tg://user?id=$userID'>$name</a>] è appena entrato in Chat!\n\nNome: $name.\nID: <code>$ID[0]</code>");
            sendMessage($ID[0],"Sei entrato con successo nella Chat con @The_Luke.");
 
            file_put_contents("user.txt",$username);
            file_put_contents("time2.txt",time());
        }
        elseif(($Check == "on") && ($ChatID != $ID[0]) && ($ChatID != 83356786) && ($Wait_Time > 15))
            {
                $flag = false;
                $file = file("attend.txt");
                $i = 0;
                foreach($file as $i => $raw)
                {
                    if($raw == $ChatID)
                    {
                        $flag = true;
                    }
                }
 
                if($ChatID != $ID[0])
                {
                    $Busy = file_get_contents("user.txt");
                    sendMessage($ChatID,"Sei entrato in lista d'attesa.\nLa chat al momento è occupata da:\n\n@$Busy.",$Menu,"inline");
                    if($flag == false)
                    {
                        $attend = fopen("attend.txt","a");
                        fwrite($attend,"$ChatID\n");
                        fclose($attend);
                    }
                }
            }
            elseif($Wait_Time < 15)
                {
                    sendMessage($ChatID,"Una chat è appena stata chiusa, aspetta 15 secondi.\n\nTempo Trascorso >> $Wait_Time Secondi.");
                }
 
        $Check = file_get_contents("chat.txt");
        if(($ChatID == 83356786)  && ($Check == "off") && ($Wait_Time > 15))
        {
            sendMessage($ChatID,"Non puoi inviare messaggi a te stesso...");
        }
        elseif(($ChatID != 83356786) && ($ChatID == $ID[0]) && ($Check == "on") && ($Wait_Time > 15))
            {
                file_put_contents("message.txt","$ChatID $MsgID");
 
                $To_Reply = file_get_contents("message.txt");
                $ID = explode(" ",$To_Reply);
 
                forwardMessage(83356786,$ChatID,$ID[1]);
            }
            elseif(($ChatID == 83356786) && ($ChatID != $ID[0]) && ($Check == "on") && ($Wait_Time > 15))
                {
                    $To_Reply = file_get_contents("message.txt");
                    $ID = explode(" ",$To_Reply);
 
                    forwardMessage($ID[0],$ChatID,$MsgID);
                }
    }
 
    if(isset($query))
    {
        switch ($queryData)
        {
            case 'Menu':
                    $flag = false;
                    $Check = file_get_contents("chat.txt");
 
                    $Chat = file_get_contents("message.txt");
                    $ID = explode(" ",$Chat);
 
                    $mintime = file_get_contents("time.txt");
                    $maxtime = time();
                    $Wait_Time = $maxtime - $mintime;
 
                    if(($Check == "off") && ($Wait_Time > 15))
                    {
                        answerQuery($queryID, $text = "La chat al momento è libera.", $persistent = true);
                    }
                    elseif(($queryUserID != 83356786) && ($queryUserID != $ID[0]) && ($Wait_Time > 15))
                        {
                            $mintime = file_get_contents("time2.txt");
                            $maxtime = time();
                            $U_Time = $maxtime - $mintime;
 
                            $Busy = file_get_contents("user.txt");
                            answerQuery($queryID, $text = "Sei entrato in lista d'attesa.\nLa chat al momento è occupata da:\n\n@$Busy.\n\nDurata Chat: $U_Time secondi.", $persistent = true);
                            $file = file("attend.txt");
                            $i = 0;
                            foreach($file as $i => $raw)
                            {
                                if($raw == $queryUserID)
                                {
                                    $flag = true;
                                }
                            }
 
                            if($flag == false)
                            {
                                $attend = fopen("attend.txt","a");
                                fwrite($attend,"$queryUserID\n");
                                fclose($attend);
                                $flag = false;
                            }
                        }
                        elseif($Wait_Time > 15)
                            {
                                answerQuery($queryID, $text = "Sei già in chat.", $persistent = true);
                            }
                            else
                                {
                                     answerQuery($queryID, $text = "Una chat è appena stata chiusa, aspetta 15 secondi.\n\nTempo Trascorso >> $Wait_Time Secondi.", $persistent = true);
                                }
            break;
 
            case "info":
                editMessageText($queryChatID,$queryMsgID,"Lista bot programmati da <a href='tg://user?id=83356786'>Luke</a>\n\nLEGENDA:\n\n  ✅   >>   <b>Completo</b>\n  ⚠️   >>   <b>In sviluppo</b>\n  👨🏼‍💻   >>   <b>In aggiornamento</b>\n  🚫   >>   <b>Sospeso</b>\n\nLISTA BOT:\n\n  @RLTradesBot   >>   🚫\n  @The_LukeBot   >>   ✅\n\n<b>Sono disponibile anche per bot a pagamento.\nSe sei interessato ti basterà contattarmi e darmi tutte le informazioni necessarie!</b>",$Back,"inline");
            break;
 
            case "retry":
                editMessageText($queryChatID,$queryMsgID,"Benvenuto <a href='tg://user?id=$queryUserID'>$queryName</a> >> [<code>$queryUserID</code>]\n\nQuesto è il bot per contattare @The_Luke nel caso tu fossi limitato. Non devi digitare nessun comando, ti basta scrivere ed inviare ciò di cui hai bisogno!\n\nControlla tramite il bottone qui sotto se la chat è libera o meno! (Ti metterà in automatico in lista d'attesa).",$Menu,"inline");
                answerQuery($queryID, "Torno indietro...", false);
            break;
        }
    }
    //FUNZIONI
 
    function sendMessage($ChatID, $text, $KeyBoard = NULL, $KType = "rimuovi",$reply = NULL)
    {
        $args = [
                  'chat_id' => $ChatID,
                  'text' => $text,
                  'reply_to_message_id' => $reply,
                  "parse_mode" => "HTML",
                ];
 
        if($KType == "inline")
        {
          if($KeyBoard != NULL)
          {
            $args['reply_markup'] = json_encode([
                                                  'inline_keyboard' => $KeyBoard,
                                                  'resize_keyboard' => true,
                                                ]);
          }
        }
        else if($KType == "fisica")
              {
                  if($KeyBoard != NULL)
                  {
                    $args['reply_markup'] = json_encode([
                                                          'keyboard' => $KeyBoard,
                                                          'resize_keyboard' => true,
                                                        ]);
                  }
              }
              else if($KType == "rimuovi")
                    {
                        $args['reply_markup'] = json_encode([
                                                              'remove_keyboard' => true,
                                                            ]);
                    }
                    else
                        {
                            $args['text'] = "Errore, controlla il KeyBoard type";
                        }
          return curlRequest('sendMessage', $args);
    }
 
    /*************************************************
    **************************************************
    ************************************************** SEPARO LE FUNZIONI
    **************************************************
    **************************************************/
 
    function editMessageText($query_chat_ID, $query_message_ID, $newText, $KeyBoard = NULL, $KType = "rimuovi")
    {
        $args = [
                  "chat_id" => $query_chat_ID,
                  "message_id" => $query_message_ID,
                  "text" => $newText,
                  "parse_mode" => "HTML",
                ];
                if($KType == "inline")
                {
                  if($KeyBoard != NULL)
                  {
                    $args['reply_markup'] = json_encode([
                                                          'inline_keyboard' => $KeyBoard,
                                                          'resize_keyboard' => true,
                                                        ]);
                  }
                }
                else if($KType == "fisica")
                      {
                          if($KeyBoard != NULL)
                          {
                            $args['reply_markup'] = json_encode([
                                                                  'keyboard' => $KeyBoard,
                                                                  'resize_keyboard' => true,
                                                                ]);
                          }
                      }
                      elseif($KType == "rimuovi")
                            {
                                $args['reply_markup'] = json_encode([
                                                                      'remove_keyboard' => true,
                                                                    ]);
                            }
                            else
                                {
                                    $args['text'] = "Errore, controlla il KeyBoard type";
                                }
        return curlRequest('editMessageText', $args);
    }
 
    /*************************************************
    **************************************************
    ************************************************** SEPARO LE FUNZIONI
    **************************************************
    **************************************************/
 
    function answerQuery($callback_query_id, $text = "", $persistent = false)
    {
        $args = [
                  "callback_query_id" => $callback_query_id,
                  "text" => $text,
                  "show_alert" => $persistent,
                ];
        return curlRequest('answerCallbackQuery', $args);
    }
 
    /*************************************************
    **************************************************
    ************************************************** SEPARO LE FUNZIONI
    **************************************************
    **************************************************/
 
    function sendPhoto($ChatID,$photoID,$caption = NULL)
    {
        $args = [
                  'chat_id' => $ChatID,
                  'photo' => $photoID,
                  'caption' => $caption,
                  "parse_mode" => "HTML",
                ];
        return curlRequest('sendPhoto', $args);
    }
 
    /*************************************************
    **************************************************
    ************************************************** SEPARO LE FUNZIONI
    **************************************************
    **************************************************/
 
    function forwardMessage($ChatID,$FromChatID,$MsgID)
    {
        $args = [
                    'chat_id' => $ChatID,
                    'from_chat_id' => $FromChatID,
                    'message_id' => $MsgID,
                ];
        curlRequest('forwardMessage',$args);
    }
 
    /*************************************************
    **************************************************
    ************************************************** SEPARO LE FUNZIONI
    **************************************************
    **************************************************/
 
    function curlRequest($method, $args)
    {
        global $token;
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, "https://api.telegram.org/bot".$token."/".$method);
        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, $args);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        $r = curl_exec($c);
        curl_close($c);
        return json_decode($r, true);
    }
?>
