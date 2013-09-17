  </div></section>

  <!-- Footer -->
  <footer><div class="row">
  <div class="twelve columns"><hr />
      <div class="row">
        <div class="three columns">
          <p>This site is a repository of tools.</p>
          <p>This is also an experiment to create a site based on Pluton, Foundation, Git <span class="ampersand">&</span> Nasqueron.</p>
        </div>
        <div class="three columns">
        	<dl>
			<dt>Gerrit</dt>
			<dd><a href="/wikimedia/dev/feeds/">Activity feeds</a></dd>
			<dd>RSS generator</dd>
        	</dl>
        </div>
        <div class="three columns">
        	<dl>
			<dt>Network</dt>
			<dd><a href="/network/mx">MX</a></dd>

			<dt>Lists</dt>
			<dd><a href="/lists/operations">Lists operations</a></dd>
			<dd><a href="/lists/replace">Lists RegExp replace</a></dd>

			<dt>Color</dt>
			<dd><a href="/color/screen/">Random color screen</a></dd>
			<dd><a href="/color/screen/879497">Gray-blue screen</a></dd>
        	</dl>
        </div>
        <div class="three columns">
        	<dl>
			<dt>Gadgets</dt>
			<dd><a href="/gadgets/motd-variations">MOTD in Jive <span class="ampersand">&</span> Valspeak</a></dd>

			<dt>Start pages</dt>
			<dd><a href="/lex">Lex</a></dd>
        	</dl>
        </div>
      </div>
  </div>
  <div class="twelve columns"><hr />
      <div class="row extrainfos">
           <div class="six columns">
               <p><i class="general foundicon-settings"></i> <strong>Options:</strong> <a href="javascript:SetUITonality('dark');">dark mode</a> | <a href="javascript:SetUITonality('light');">light mode</a></p>
           </div>
      </div>
      <div class="row extrainfos">
        <div class="six columns">
            <p><i class="general foundicon-globe"></i> <strong>Crafted by</strong> <a href="http://www.dereckson.be/">Dereckson</a> | <strong>Powered by</strong> <a href="http://keruald.sf.net">Keruald/Pluton</a> <span class="ampersand">&</span> <a href="http://foundation.zurb.com/">Foundation</a>.</p>
        </div>

        <div class="six columns">
            <p class="right"><i class="general foundicon-flag"></i> <strong>Git revision:</strong> <?= substr(`git rev-parse HEAD`, 0, 7) ?> | <strong>Version:</strong> alpha preview</p>
        </div>

      </div>
  </div>
  </div></footer>

  <script src="/javascripts/foundation.min.js"></script>
  <script src="/javascripts/jquery.cookie.js"></script>
  <script src="/javascripts/app.js"></script>
  <?= $document->footer ?>
</body>
</html>
