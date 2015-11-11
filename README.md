#Sphinx xmlpipe2 in PHP

http://jetpackweb.com/blog/2009/08/16/sphinx-xmlpipe2-in-php-part-i/#<br>
http://jetpackweb.com/blog/2009/08/16/sphinx-xmlpipe2-in-php-part-ii/#

An example xmlpipe2 format looks like this:
```xml
<?xml version="1.0" encoding="utf-8"?>
<sphinx:docset>

<sphinx:schema>
  <sphinx:field name="subject"/>
  <sphinx:field name="content"/>
  <sphinx:attr name="published" type="timestamp"/>
  <sphinx:attr name="author_id" type="int" bits="16" default="1"/>
</sphinx:schema>

<sphinx:document id="1234">
  <content>this is the main content <![CDATA[[and this <cdata> entry must be handled properly by xml parser lib]]></content>
  <published>1012325463</published>
  <subject>note how field/attr tags can be in <b class="red">randomized</b> order</subject>
  <misc>some undeclared element</misc>
</sphinx:document>
<!-- ... more documents here ... -->
</sphinx:docset>
```

You would setup you datasource in sphinx.conf something like this:
```
source xml_blog_posts
{
    type = xmlpipe
    xmlpipe_command = /usr/bin/php /home/example.com/lib/tasks/sphinx_blogs.php
}
```

Here is a class that extends XMLWriter, which is a built in PHP class that is essentially undocumented and works great for creating memory efficient streams of XML data.
Rather than keeping each document in memory, XMLWriter will allow us to immediately flush that document’s XML elements to standard output.

##Usage
We can use it as follows:

```php
$doc = new SphinxXMLFeed();

$doc->setFields(array(
  'title',
  'teaser',
  'content',
));

$doc->setAttributes(array(
  array('name' => 'blog_id', 'type' => 'int', 'bits' => '16', 'default' => '0'),
));

$doc->beginOutput();

foreach(range(1, 1000) as $id) {
  $doc->addDocument(array(
    'id' => $id,
    'blog_id' => rand(1, 10),
    'title' => "Article Part {$id}",
    'teaser' => "Article {$id} teaster",
    'content' => "Article {$id} content",
  ));
}

$doc->endOutput();
```

As you can see the first thing we need to do is populate the fields and attributes. Once that is done, we call beginOutput, that will create the head of the XML document. After each document is added, the document’s xml markup is immediately outputted and the memory buffer is cleared.

Finally we call endOutput, which will close the sphinx:docset element.

I have used this class in production to index millions of records that take up dozens of gigabytes. Keep in mind if you are working with that much data, you will probably need to bach your queries so you are not loading all the records at once!

