<?php

namespace cls;

class OpenAI {
  static function get_open_ai_key(): string {
    return file_get_contents("/home/majo/Desktop/open_ai_key.txt");
  }

  # todo
  // simple ai request here for embeddings and chat gpt stuff ...

  static function test_embeddings() {
    /*
     curl https://api.openai.com/v1/embeddings \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $OPENAI_API_KEY" \
      -d '{
        "input": "Your text string goes here",
        "model": "text-embedding-ada-002"
      }'
     */

    $text = <<<EOT
    Kant und seine Philosophie: Ein Testbeitrag

Immanuel Kant (1724-1804) war ein bedeutender deutscher Philosoph der Aufklärung, dessen Ideen das Fundament für die moderne Philosophie gelegt haben. Seine Werke, insbesondere die "Kritik der reinen Vernunft" und die "Grundlegung zur Metaphysik der Sitten", haben das Denken und die Diskussionen in der Philosophie nachhaltig beeinflusst. Dieser Testbeitrag wird einige der wichtigsten Konzepte und Ideen Kants beleuchten.

1. Die kategorische Imperativ:

Kant formulierte den kategorischen Imperativ als ein ethisches Prinzip, das universell und absolut gültig ist. Dieser Grundsatz verlangt von uns, dass wir nur so handeln sollen, dass unsere Handlungen als allgemeines Gesetz für alle vernünftigen Wesen gelten können. Mit anderen Worten, man sollte nur so handeln, dass die Maximen des eigenen Handelns in einer idealen Welt zu allgemeinen Gesetzen werden könnten. Dieser ethische Ansatz betont die Würde des Einzelnen und die Pflicht, moralisch zu handeln, unabhängig von den Konsequenzen.

2. Die Transzendentalphilosophie:

Kant führte die Transzendentalphilosophie ein, um die Bedingungen und Grenzen menschlicher Erkenntnis zu erforschen. Er argumentierte, dass unser Wissen von der Welt von den Bedingungen unseres eigenen Geistes abhängt. Mit anderen Worten, wir können die Welt nur durch die Filter unserer eigenen Wahrnehmung und Kategorien verstehen. Dies führte zur berühmten Unterscheidung zwischen dem "Ding an sich" (die objektive Realität) und dem, was wir durch unsere Sinne und unser Denken über diese Realität erfahren.

3. Das Prinzip der Autonomie:

Kant betonte die Idee der Autonomie als entscheidend für moralische Entscheidungen. Autonomie bedeutet, dass die moralische Pflicht aus dem inneren Willen und der Vernunft des Einzelnen entspringt, anstatt von äußeren Einflüssen oder Belohnungen gesteuert zu werden. Autonome Entscheidungen sind frei von äußerem Zwang und basieren auf der Vernunft, was Kant als den höchsten moralischen Wert betrachtete.

4. Pflichtethik:

Kants Ethik ist in erster Linie eine Pflichtethik, bei der die Handlung an sich moralisch ist, unabhängig von den Folgen. Er argumentierte, dass wir aus Pflicht handeln sollten, weil dies die einzige Art und Weise ist, moralisch zu handeln. Pflicht ist das, was uns moralisch verpflichtet, unabhängig von unseren persönlichen Neigungen oder den Konsequenzen unserer Handlungen.

5. Der kritische Idealismus:

Kant wird oft als ein Vertreter des kritischen Idealismus angesehen, da er die Idee der Idealität von Raum und Zeit in der Wahrnehmung entwickelte. Dies bedeutet, dass Raum und Zeit nicht unabhängig von unserer Wahrnehmung existieren, sondern Konzepte sind, die notwendig für unsere Erkenntnis sind.

Insgesamt hat Immanuel Kant mit seinen Theorien und Ideen das Gesicht der modernen Philosophie geprägt und eine breite Diskussion über Ethik, Erkenntnistheorie und Metaphysik angestoßen. Seine Werke sind nach wie vor von großer Bedeutung und bieten reichhaltige Ansatzpunkte für die philosophische Reflexion.

EOT;

    // use curl

    $data = [
      "input" => "Ich bin en süßer hund",#$text,
      "model" => "text-embedding-ada-002",
      "encoding_format" => "float"
    ];

    // as utf8
    $encoded = json_encode($data, JSON_UNESCAPED_UNICODE);
    $encoded = json_encode($data);



    $ch = curl_init();
    curl_setopt_array($ch, array(
      CURLOPT_URL => 'https://api.openai.com/v1/embeddings',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => false,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_POSTFIELDS => '{
        "input": "Your text string goes here",
        "model": "text-embedding-ada-002"
      }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . self::get_open_ai_key()
      ),
    ));

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);
    echo $result;

    file_put_contents("test.txt", $result);
    
  }
}

OpenAI::test_embeddings();