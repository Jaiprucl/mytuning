<?php

/*    Please retain this copyright header in all versions of the software
 *
 *    Copyright (C) Christopher Olhöft
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see {http://www.gnu.org/licenses/}.
 */

namespace CO\mailattachment\Core;
use Email_parent;
use oxRegistry;

class COEmail extends Email_parent
{
    public function sendOrderEmailToUser($order, $subject = null)
    {
        // add user defined stuff if there is any
        $order = $this->_addUserInfoOrderEMail($order);

        $shop = $this->_getShop();
        $this->_setMailParams($shop);

        $user = $order->getOrderUser();
        $this->setUser($user);

        // create messages
        $smarty = $this->_getSmarty();
        $this->setViewData("order", $order);

        // $this->setViewData("blShowReviewLink", $this->shouldProductReviewLinksBeIncluded());

        // Process view data array through oxOutput processor
        $this->_processViewArray();

        $this->setBody($smarty->fetch($this->_sOrderUserTemplate));
        $this->setAltBody($smarty->fetch($this->_sOrderUserPlainTemplate));

        // #586A
        if ($subject === null) {
            if ($smarty->template_exists($this->_sOrderUserSubjectTemplate)) {
                $subject = $smarty->fetch($this->_sOrderUserSubjectTemplate);
            } else {
                $subject = $shop->oxshops__oxordersubject->getRawValue() . " (#" . $order->oxorder__oxordernr->value . ")";
            }
        }

        $this->setSubject($subject);

        $fullName = $user->oxuser__oxfname->getRawValue() . " " . $user->oxuser__oxlname->getRawValue();

        $this->setRecipient($user->oxuser__oxusername->value, $fullName);
        $this->setReplyTo($shop->oxshops__oxorderemail->value, $shop->oxshops__oxname->getRawValue());

        $_sThisAttachmentConfig = oxRegistry::get("oxConfig");
        $_sThisAttachmentFile1 = getShopBasePath() . $_sThisAttachmentConfig->getConfigParam("CO_AGB_FILE");
        $this->addAttachment($_sThisAttachmentFile1);;
        $_sThisAttachmentFile2 = getShopBasePath() . $_sThisAttachmentConfig->getConfigParam("CO_WITHDRAWAL_FILE");
        $this->addAttachment($_sThisAttachmentFile2);

        return $this->send();
    }

    public function sendContactMail($emailAddress = null, $subject = null, $message = null)
    {
        // shop info
        $shop = $this->_getShop();
        //set mail params (from, fromName, smtp)
        $this->_setMailParams($shop);
        $this->setBody($message);
        $this->setSubject($subject);
        $this->setRecipient($shop->oxshops__oxinfoemail->value, "");
        // Original: $this->setFrom($shop->oxshops__oxowneremail->value, $shop->oxshops__oxname->getRawValue());
        //START:
        $this->setFrom("chris@istderking.com" /* $emailAddress */, "");
        //END
        $this->setReplyTo($emailAddress, "");
        return $this->send();
    }
}