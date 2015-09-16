<?php
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var $view TimedPhpEngine */
/** @var $user User */

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');

$view['slots']->stop();

$view['slots']->start('javascripts');

$view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane" id="terms">
    <div class="pane-content">
        <h2>Terms and Conditions</h2>

        <p>Agreement between user and <a href="<?php print $view['router']->generate('_welcome', [], true); ?> ">www.studysauce.com&nbsp;</a></p>

        <p>Welcome to <a href="<?php print $view['router']->generate('_welcome', [], true); ?>">www.studysauce.com</a>. The <a
                href="<?php print $view['router']->generate('_welcome', [], true); ?>">www.studysauce.com</a> website (the "Site") is comprised of various web
            pages operated by The Study Institute <a href="<?php print $view['router']->generate('_welcome', [], true); ?>">www.studysauce.com</a> is offered
            to you conditioned on your acceptance without modification of the terms, conditions, and notices contained
            herein (the "Terms"). Your use of <a href="<?php print $view['router']->generate('_welcome', [], true); ?>">www.studysauce.com</a> constitutes
            your agreement to all such Terms. Please read these terms carefully, and keep a copy of them for your
            reference.&nbsp;<a href="<?php print $view['router']->generate('_welcome', [], true); ?>">www.studysauce.com</a> is a E-commerce Site. &nbsp;We
            provide custom study plans for students. &nbsp;&nbsp;</p>
        <h4>Privacy&nbsp;</h4>

        <p>Your use of <a href="<?php print $view['router']->generate('_welcome', [], true); ?>">www.studysauce.com</a> is subject to The Study Institute's
            Privacy Policy. Please review our Privacy Policy, which also governs the Site and informs users of our data
            collection practices.&nbsp;</p>
        <h4>Electronic Communications&nbsp;</h4>

        <p>Visiting <a href="<?php print $view['router']->generate('_welcome', [], true); ?>">www.studysauce.com</a> or sending emails to The Study Institute
            constitutes electronic communications. You consent to receive electronic communications and you agree that
            all agreements, notices, disclosures and other communications that we provide to you electronically, via
            email and on the Site, satisfy any legal requirement that such communications be in writing.&nbsp;</p>
        <h4>Your account&nbsp;</h4>

        <p>If you use this site, you are responsible for maintaining the confidentiality of your account and password
            and for restricting access to your computer, and you agree to accept responsibility for all activities that
            occur under your account or password. You may not assign or otherwise transfer your account to any other
            person or entity. You acknowledge that The Study Institute is not responsible for third party access to your
            account that results from theft or misappropriation of your account. The Study Institute and its associates
            reserve the right to refuse or cancel service, terminate accounts, or remove or edit content in our sole
            discretion.&nbsp;</p>

        <p>The Study Institute does not knowingly collect, either online or offline, personal information from persons
            under the age of thirteen. If you are under 18, you may use <a href="<?php print $view['router']->generate('_welcome', [], true); ?>">www.studysauce.com</a>
            only with permission of a parent or guardian.&nbsp;</p>
        <h4>Cancellation/Refund Policy&nbsp;</h4>

        <p>If you are not completely satisfied with your study plan, we will help adjust the plan until you are happy.
            If you just don't like the product, we are happy to refund your money.&nbsp;&nbsp;</p>
        <h4>Links to third party sites/Third party services&nbsp;</h4>

        <p><a href="<?php print $view['router']->generate('_welcome', [], true); ?>">www.studysauce.com</a> may contain links to other websites ("Linked
            Sites"). The Linked Sites are not under the control of The Study Institute and The Study Institute is not
            responsible for the contents of any Linked Site, including without limitation any link contained in a Linked
            Site, or any changes or updates to a Linked Site. The Study Institute is providing these links to you only
            as a convenience, and the inclusion of any link does not imply endorsement by The Study Institute of the
            site or any association with its operators.&nbsp;</p>

        <p>Certain services made available via <a href="<?php print $view['router']->generate('_welcome', [], true); ?>">www.studysauce.com</a> are delivered
            by third party sites and organizations. By using any product, service or functionality originating from the
            <a href="<?php print $view['router']->generate('_welcome', [], true); ?>">www.studysauce.com</a> domain, you hereby acknowledge and consent that
            The Study Institute may share such information and data with any third party with whom The Study Institute
            has a contractual relationship to provide the requested product, service or functionality on behalf of <a
                href="<?php print $view['router']->generate('_welcome', [], true); ?>">www.studysauce.com</a> users and customers.&nbsp;</p>
        <h4>No unlawful or prohibited use/Intellectual Property&nbsp;</h4>

        <p>You are granted a non-exclusive, non-transferable, revocable license to access and use <a
                href="<?php print $view['router']->generate('_welcome', [], true); ?>">www.studysauce.com</a> strictly in accordance with these terms of use.
            As a condition of your use of the Site, you warrant to The Study Institute that you will not use the Site
            for any purpose that is unlawful or prohibited by these Terms. You may not use the Site in any manner which
            could damage, disable, overburden, or impair the Site or interfere with any other party's use and enjoyment
            of the Site. You may not obtain or attempt to obtain any materials or information through any means not
            intentionally made available or provided for through the Site.&nbsp;&nbsp;</p>

        <p>All content included as part of the Service, such as text, graphics, logos, images, as well as the
            compilation thereof, and any software used on the Site, is the property of The Study Institute or its
            suppliers and protected by copyright and other laws that protect intellectual property and proprietary
            rights. You agree to observe and abide by all copyright and other proprietary notices, legends or other
            restrictions contained in any such content and will not make any changes thereto.&nbsp;</p>

        <p>You will not modify, publish, transmit, reverse engineer, participate in the transfer or sale, create
            derivative works, or in any way exploit any of the content, in whole or in part, found on the Site. The
            Study Institute content is not for resale. Your use of the Site does not entitle you to make any
            unauthorized use of any protected content, and in particular you will not delete or alter any proprietary
            rights or attribution notices in any content. You will use protected content solely for your personal use,
            and will make no other use of the content without the express written permission of The Study Institute and
            the copyright owner. You agree that you do not acquire any ownership rights in any protected content. We do
            not grant you any licenses, express or implied, to the intellectual property of The Study Institute or our
            licensors except as expressly authorized by these Terms.&nbsp;&nbsp;</p>
        <h4>International Users&nbsp;</h4>

        <p>The Service is controlled, operated and administered by The Study Institute from our offices within the USA.
            If you access the Service from a location outside the USA, you are responsible for compliance with all local
            laws. You agree that you will not use the The Study Institute Content accessed through <a
                href="<?php print $view['router']->generate('_welcome', [], true); ?>">www.studysauce.com</a> in any country or in any manner prohibited by
            any applicable laws, restrictions or regulations.&nbsp;</p>
        <h4>Indemnification&nbsp;</h4>

        <p>You agree to indemnify, defend and hold harmless The Study Institute, its officers, directors, employees,
            agents and third parties, for any losses, costs, liabilities and expenses (including reasonable attorneys'
            fees) relating to or arising out of your use of or inability to use the Site or services, any user postings
            made by you, your violation of any terms of this Agreement or your violation of any rights of a third party,
            or your violation of any applicable laws, rules or regulations. The Study Institute reserves the right, at
            its own cost, to assume the exclusive defense and control of any matter otherwise subject to indemnification
            by you, in which event you will fully cooperate with The Study Institute in asserting any available
            defenses.&nbsp;</p>
        <h4>Liability disclaimer&nbsp;</h4>

        <p>THE INFORMATION, SOFTWARE, PRODUCTS, AND SERVICES INCLUDED IN OR AVAILABLE THROUGH THE SITE MAY INCLUDE
            INACCURACIES OR TYPOGRAPHICAL ERRORS. CHANGES ARE PERIODICALLY ADDED TO THE INFORMATION HEREIN. THE STUDY
            INSTITUTE AND/OR ITS SUPPLIERS MAY MAKE IMPROVEMENTS AND/OR CHANGES IN THE SITE AT ANY TIME.&nbsp;</p>

        <p>THE STUDY INSTITUTE AND/OR ITS SUPPLIERS MAKE NO REPRESENTATIONS ABOUT THE SUITABILITY, RELIABILITY,
            AVAILABILITY, TIMELINESS, AND ACCURACY OF THE INFORMATION, SOFTWARE, PRODUCTS, SERVICES AND RELATED GRAPHICS
            CONTAINED ON THE SITE FOR ANY PURPOSE. TO THE MAXIMUM EXTENT PERMITTED BY APPLICABLE LAW, ALL SUCH
            INFORMATION, SOFTWARE, PRODUCTS, SERVICES AND RELATED GRAPHICS ARE PROVIDED "AS IS" WITHOUT WARRANTY OR
            CONDITION OF ANY KIND. THE STUDY INSTITUTE AND/OR ITS SUPPLIERS HEREBY DISCLAIM ALL WARRANTIES AND
            CONDITIONS WITH REGARD TO THIS INFORMATION, SOFTWARE, PRODUCTS, SERVICES AND RELATED GRAPHICS, INCLUDING ALL
            IMPLIED WARRANTIES OR CONDITIONS OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, TITLE AND
            NON-INFRINGEMENT.&nbsp;</p>

        <p>TO THE MAXIMUM EXTENT PERMITTED BY APPLICABLE LAW, IN NO EVENT SHALL THE STUDY INSTITUTE AND/OR ITS SUPPLIERS
            BE LIABLE FOR ANY DIRECT, INDIRECT, PUNITIVE, INCIDENTAL, SPECIAL, CONSEQUENTIAL DAMAGES OR ANY DAMAGES
            WHATSOEVER INCLUDING, WITHOUT LIMITATION, DAMAGES FOR LOSS OF USE, DATA OR PROFITS, ARISING OUT OF OR IN ANY
            WAY CONNECTED WITH THE USE OR PERFORMANCE OF THE SITE, WITH THE DELAY OR INABILITY TO USE THE SITE OR
            RELATED SERVICES, THE PROVISION OF OR FAILURE TO PROVIDE SERVICES, OR FOR ANY INFORMATION, SOFTWARE,
            PRODUCTS, SERVICES AND RELATED GRAPHICS OBTAINED THROUGH THE SITE, OR OTHERWISE ARISING OUT OF THE USE OF
            THE SITE, WHETHER BASED ON CONTRACT, TORT, NEGLIGENCE, STRICT LIABILITY OR OTHERWISE, EVEN IF THE STUDY
            INSTITUTE OR ANY OF ITS SUPPLIERS HAS BEEN ADVISED OF THE POSSIBILITY OF DAMAGES. BECAUSE SOME
            STATES/JURISDICTIONS DO NOT ALLOW THE EXCLUSION OR LIMITATION OF LIABILITY FOR CONSEQUENTIAL OR INCIDENTAL
            DAMAGES, THE ABOVE LIMITATION MAY NOT APPLY TO YOU. IF YOU ARE DISSATISFIED WITH ANY PORTION OF THE SITE, OR
            WITH ANY OF THESE TERMS OF USE, YOUR SOLE AND EXCLUSIVE REMEDY IS TO DISCONTINUE USING THE SITE.&nbsp;&nbsp;&nbsp;</p>
        <h4>Termination/access restriction&nbsp;</h4>

        <p>The Study Institute reserves the right, in its sole discretion, to terminate your access to the Site and the
            related services or any portion thereof at any time, without notice. To the maximum extent permitted by law,
            this agreement is governed by the laws of the State of Arizona and you hereby consent to the exclusive
            jurisdiction and venue of courts in Arizona in all disputes arising out of or relating to the use of the
            Site. Use of the Site is unauthorized in any jurisdiction that does not give effect to all provisions of
            these Terms, including, without limitation, this section.&nbsp;</p>

        <p>You agree that no joint venture, partnership, employment, or agency relationship exists between you and The
            Study Institute as a result of this agreement or use of the Site. The Study Institute's performance of this
            agreement is subject to existing laws and legal process, and nothing contained in this agreement is in
            derogation of The Study Institute's right to comply with governmental, court and law enforcement requests or
            requirements relating to your use of the Site or information provided to or gathered by The Study Institute
            with respect to such use. If any part of this agreement is determined to be invalid or unenforceable
            pursuant to applicable law including, but not limited to, the warranty disclaimers and liability limitations
            set forth above, then the invalid or unenforceable provision will be deemed superseded by a valid,
            enforceable provision that most closely matches the intent of the original provision and the remainder of
            the agreement shall continue in effect.&nbsp;</p>

        <p>Unless otherwise specified herein, this agreement constitutes the entire agreement between the user and The
            Study Institute with respect to the Site and it supersedes all prior or contemporaneous communications and
            proposals, whether electronic, oral or written, between the user and The Study Institute with respect to the
            Site. A printed version of this agreement and of any notice given in electronic form shall be admissible in
            judicial or administrative proceedings based upon or relating to this agreement to the same extent an d
            subject to the same conditions as other business documents and records originally generated and maintained
            in printed form. It is the express wish to the parties that this agreement and all related documents be
            written in English.&nbsp;</p>
        <h4>Changes to Terms&nbsp;</h4>

        <p>The Study Institute reserves the right, in its sole discretion, to change the Terms under which <a
                href="<?php print $view['router']->generate('_welcome', [], true); ?>">www.studysauce.com</a> is offered. The most current version of the
            Terms will supersede all previous versions. The Study Institute encourages you to periodically review the
            Terms to stay informed of our updates.&nbsp;&nbsp;&nbsp;</p>

        <h3>Contact Us&nbsp;</h3>

        <p>The Study Institute welcomes your questions or comments regarding the Terms:&nbsp;</p>

        <p>The Study Institute&nbsp;</p>

        <p>19621 N 96th Pl&nbsp;</p>

        <p>Scottsdale, Arizona 85255&nbsp;&nbsp;&nbsp;</p>

        <p>Email Address:&nbsp;</p>

        <p><a href="mailto:support@studysauce.com">support@studysauce.com</a>&nbsp;</p>

        <p>&nbsp;(480) 331-8570</p>

        <p>&nbsp;</p>

        <p>Effective as of September 09, 2013&nbsp;</p>

        <div class="highlighted-link"><a href="<?php print $view['router']->generate('_welcome'); ?>" class="more">Go home</a></div>
    </div>
</div>
<?php $view['slots']->stop(); ?>
