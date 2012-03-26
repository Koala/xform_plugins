h1. REXDEV XForm Plugins

Das Plugin klinkt sich in das Backend Menü von XForm ein, und fügt die zusätzliche subpage @Plugins@ hinzu. Die im Plugin hinterlegten Klassen werden in XForm included, und stehen mit ihren Funktionen jedem XForm Formular zur Verfügung. Darüberhinaus können Plugins/Klassen eigene Settings verwalten, wie z.b. das @reCaptcha@ Plugin.

h2. Verwendung der Klassen ohne das Plugins

Die Klassen sind autonom in ihrer Funktion, und können auch ohne das Plugin direkt in _XForm_ selbst verwendet. Dazu müssen die jeweiligen Klassen in das korrespondiere Verzeichnis des _XForm Addons_ kopiert werden. *Werden die Klassen in dieser Form verwendet, stehen die zusätzlichen Funktion wie Einstellungen oder erweiterte Hilfetexte nicht zur Verfügung.*


h3. installierte Plugins:

* VALUE:
## recaptcha
## onclick_clear_value
## mailfrom
## textile
## selectplus
## textplus
* VALIDATE:
## date_diff
## strip_html

"rexdev.de":http://rexdev.de