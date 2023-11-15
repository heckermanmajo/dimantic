<?php

namespace tests\tests\lib;

class TestPosts {
  static function create() {

    $poems = [
      "
    Ich bin der Geist, der stets verneint!
    Und das mit Recht; denn alles, was entsteht,
    Ist wert, daß es zugrunde geht;
    Drum besser wär's, daß nichts entstünde.
    So ist denn alles, was ihr Sünde,
    Zerstörung, kurz, das Böse nennt,
    Mein eigentliches Element.
    ",
      "
    O Mensch! Gib acht!
    Was spricht die tiefe Mitternacht?
    »Ich schlief, ich schlief—,
    Aus tiefem Traum bin ich erwacht:—
    Die Welt ist tief,
    Und tiefer als der Tag gedacht.
    Tief ist ihr Weh—,
    Lust—tiefer noch als Herzeleid:
    Weh spricht: Vergeh!
    Doch alle Lust will Ewigkeit—,
    —will tiefe, tiefe Ewigkeit!«
    ",
      "Rosen sind rot, Veilchen sind blau,
    Auf dieser Wiese, da finde ich Ruh'.
    Die Vögel sie singen, die Sonne lacht,
    Hier sitz ich und träum', die Zeit verrinnt sacht.",

      "Im Wald, da rauscht es leise,
    Die Bäume wiegen sich im Kreise.
    Ein Vogel singt sein Lied so hell,
    Die Natur ist wunderbar, das fühl' ich schnell.",

      "Am Strand, der Sand so fein,
    Ich zähle Muscheln, eins, zwei, drein.
    Die Wellen rauschen, das Meer so weit,
    Hier bin ich glücklich, in dieser Zeit.",

      "In der Stadt, das Leben so bunt,
    Die Menschen eilen, sind stets in Rund.
    Doch auch hier gibt es Pausen klein,
    Wo Ruhe und Gelassenheit können sein.",

      "Berge hoch, der Himmel so blau,
    Ich steig empor, hab keine Ruh'.
    Die Aussicht grandios, die Welt so weit,
    Hier oben fühle ich mich befreit.",

      "Im Regen, die Tropfen auf meiner Haut,
    Die Welt wird still, die Natur vertraut.
    Der Duft der Erde, der Klang des Regens,
    Hier gibt es keinen Grund für Unbehagen.",

      "Im Winter, die Landschaft so weiß,
    Die Kälte beißt, der Schnee ist leis'.
    Doch in der Stille, im Winterglanz,
    Find ich Frieden und neuen Lebenskranz.",

      "Zu Hause, in meinem gemütlichen Raum,
    Mit Kerzenlicht und warmem Schaum.
    Ein Buch in der Hand, die Welt verweilend,
    Hier kann die Seele sich sanft entfaltend."
    ];


    $league_1 = \cls\data\league\AttentionLeague::get_by_id(
      \App::get_connection(),
      1
    );

    for ($i = 1; $i < count($poems); $i++) {

      $gedicht = new \cls\data\post\Post();
      $gedicht->content = $poems[$i - 1];
      $gedicht->author_id = $i;
      $gedicht->published = 1;
      $gedicht->idea_space_id = 1;
      $gedicht->liga_season_id = $league_1->get_latest_season()->id;
      $gedicht->save(\App::get_connection());

      /*
      $attention_dimension_entry_gedicht = new \cls\data\AttentionDimensionEntry();
      $attention_dimension_entry_gedicht->attention_dimension_id = TestAttentionDimensions::LYRISCHE_SCHOENHEIT;
      $attention_dimension_entry_gedicht->post_id = $gedicht->id;
      $attention_dimension_entry_gedicht->save(\App::get_connection());

      $attention_dimension_entry_gedicht = new \cls\data\AttentionDimensionEntry();
      $attention_dimension_entry_gedicht->attention_dimension_id = TestAttentionDimensions::RELEVANZ_FOR_DIE_GESELLSCHAFT;
      $attention_dimension_entry_gedicht->post_id = $gedicht->id;
      $attention_dimension_entry_gedicht->save(\App::get_connection());

      $attention_dimension_entry_gedicht = new \cls\data\AttentionDimensionEntry();
      $attention_dimension_entry_gedicht->attention_dimension_id = TestAttentionDimensions::SPRACHLICHE_KREATIVITAET;
      $attention_dimension_entry_gedicht->post_id = $gedicht->id;
      $attention_dimension_entry_gedicht->save(\App::get_connection());
      */
    }


    $league_1 = \cls\data\league\AttentionLeague::get_by_id(
      \App::get_connection(),
      1
    );

    for ($i = 1; $i < count($poems); $i++) {

      $gedicht = new \cls\data\post\Post();
      $gedicht->content = $poems[$i - 1];
      $gedicht->author_id = $i;
      $gedicht->published = 1;
      $gedicht->idea_space_id = 2;
      $gedicht->liga_season_id = $league_1->get_latest_season()->id;
      $gedicht->save(\App::get_connection());
    }


    $league_1 = \cls\data\league\AttentionLeague::get_by_id(
      \App::get_connection(),
      1
    );

    for ($i = 1; $i < count($poems); $i++) {
      $gedicht = new \cls\data\post\Post();
      $gedicht->content = $poems[$i - 1];
      $gedicht->author_id = $i;
      $gedicht->published = 1;
      $gedicht->idea_space_id = 3;
      $gedicht->liga_season_id = $league_1->get_latest_season()->id;
      $gedicht->save(\App::get_connection());
    }

    $league_1 = \cls\data\league\AttentionLeague::get_by_id(
      \App::get_connection(),
      1
    );

    for ($i = 1; $i < count($poems); $i++) {
      $gedicht = new \cls\data\post\Post();
      $gedicht->content = $poems[$i - 1];
      $gedicht->author_id = $i;
      $gedicht->published = 1;
      $gedicht->idea_space_id = 4;
      $gedicht->liga_season_id = $league_1->get_latest_season()->id;
      $gedicht->save(\App::get_connection());
    }

  }


}