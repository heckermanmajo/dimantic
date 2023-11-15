<?php

namespace tests\tests\lib;

class TestAttentionDimensions {
  static function create(): void {

    $lyrische_schoenheit = new \cls\data\league\AttentionDimension();
    $lyrische_schoenheit->title = "Lyrische Schönheit";
    $lyrische_schoenheit->description = "Wie schön ist die Lyrik?";
    $lyrische_schoenheit->save(\App::get_connection());

    $relevanz_for_die_gesellschaft = new \cls\data\league\AttentionDimension();
    $relevanz_for_die_gesellschaft->title = "Relevanz für die Gesellschaft";
    $relevanz_for_die_gesellschaft->description = "Wie relevant ist die Lyrik für die Gesellschaft?";
    $relevanz_for_die_gesellschaft->save(\App::get_connection());

    $quellen_belege = new \cls\data\league\AttentionDimension();
    $quellen_belege->title = "Quellen und Belege";
    $quellen_belege->description = "Wie gut sind die Quellen und Belege?";
    $quellen_belege->save(\App::get_connection());

    $sprachliche_kreativitaet = new \cls\data\league\AttentionDimension();
    $sprachliche_kreativitaet->title = "Sprachliche Kreativität";
    $sprachliche_kreativitaet->description = "Wie kreativ ist die Sprache?";
    $sprachliche_kreativitaet->save(\App::get_connection());

    $story_telling = new \cls\data\league\AttentionDimension();
    $story_telling->title = "Story Telling";
    $story_telling->description = "Wie gut ist die Story?";
    $story_telling->save(\App::get_connection());

  }
}