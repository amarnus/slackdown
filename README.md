# Slackdown

## Synopsis

Translates text written in Slack-style markdown to HTML.

---

## Usage

```php
use \Text\Slackdown as Slackdown;

$subject = '>>>*Today is a good day...*\n\n>Yes, it is\n-_God_';
$slackDown = new Slackdown();
echo $slackDown->process($subject);
```

---

## Options

All of these options are set to `TRUE` by default.

- `bold`
- `italics`
- `strike`
- `fmt_inline`
- `fmt_multiline`
- `blockquote`
- `blockquote_multiline`

---

## Author

Amarnath Ravikumar &lt;amarnus@gmail.com&gt;
