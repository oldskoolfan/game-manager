<?php
/**
 * Our game class, which does two things for us:
 *  1. It helps map data in PHP to our game table in MySQL simply by existing
 *     as as datatype with properties that match the columns.
 *  2. It has some validation and helper methods to assist with saving data
 *     to MySQL.
 */
class Game {

  // properties
  public $id;
  public $title;
  public $year;
  public $beaten = false; // shows now to declare an initial value
  public $system;
  public $developer;
  public $categories;

  /**
   * Constructors are always named __construct, starting with two underscores.
   * Here we've added six parameters to initialize the six properties on our
   * object.
   */
  public function __construct($gameId = null, $gameTitle = '', $releaseYear = '',
    $hasCompleted = false, $systemId = '', $devId = '', $categories = []) {
    $this->id = $gameId;
    $this->title = $gameTitle;
    $this->year = $releaseYear;
    $this->beaten = $hasCompleted;
    $this->system = $systemId;
    $this->developer = $devId;
    $this->categories = $categories;
  }

  /**
   * This function will tell us if we have everything we need to save a game
   * to the database or not.
   */
  public function hasAllValues() {
    return !empty($this->title) && !empty($this->year) && !empty($this->system) &&
      !empty($this->developer);
  }

  /**
   * This function is a simple test of whether or not the year is numeric on
   * this game object.
   */
  public function yearIsNumeric() {
    return is_numeric($this->year);
  }

  /**
   * This function will generate the SQL necessary to save the game to the
   * database. Depending on whether the game has an ID, it will return either
   * an update (yes) or an insert (no) statement.
   */
  public function getQuery() {
    // note the curly braces where we call a method inside the double quotes
    if ($this->haveGameId()) {
      return "update game
        set title = '$this->title',
        release_year = '$this->year',
        beaten = {$this->beatenAsInt()},
        system_id = $this->system,
        developer_id = $this->developer
        where game_id = $this->id";
    } else {
      return "insert game (system_id, developer_id, title, release_year, beaten)
        values($this->system, $this->developer, '$this->title', '$this->year',
        {$this->beatenAsInt()})";
    }
  }

  public function getStatement(&$con) {
    $title = $this->title;
    $year = $this->year;
    $beaten = $this->beatenAsInt();
    $system = $this->system;
    $dev = $this->developer;
    $id = $this->id;

    // if query isn't working, var_dump($q) is helpful
    if ($this->haveGameId()) {
      $q = "update game set title = ?, release_year = ?, beaten = ?, system_id = ?,
        developer_id = ? where game_id = ?";
      $stmt = $con->prepare($q);
      $stmt->bind_param('ssiiii', $title, $year, $beaten, $system, $dev, $id);
    } else {
      $q = "insert game (system_id, developer_id, title, release_year, beaten)
        values(?, ?, ?, ?, ?)";
      $stmt = $con->prepare($q);
      $stmt->bind_param('iissi', $system, $dev, $title, $year, $beaten);
    }

    return $stmt;
  }

  public function saveCategories(&$con) {
    if (!$this->haveGameId() || count($this->categories) === 0) {
      return;
    }

    // delete any game categories that exist but are no longer part of our list
    $gameId = $this->id;
    $catIds = implode(',', $this->categories);
    $q = "delete from game_category where game_id = ?";
    $stmt = $con->prepare($q);
    $stmt->bind_param('i', $gameId);
    
    if (!$stmt->execute()) {
      throw new \Exception($stmt->error);
    }

    // insert a new game_category record for every category id
    $catId = 0;
    $stmt = $con->prepare('insert into game_category values (?, ?)');
    $stmt->bind_param('ii', $gameId, $catId);

    foreach($this->categories as $category) {
      $catId = $category;
      if (!$stmt->execute()) {
        throw new \Exception($stmt->error);
      }
    }
  }

  /**
   * a static helper function to do some setup mysql calls for the game form page
   */
  public static function initGameForm(&$con) {
    try {
      // get the ID from the URL if there is one
      $id = $_GET['id'] ?? null;

      // get developers and systems for our dropdown options
      $developers = $con->query('select * from developer');
      $systems = $con->query('select * from system');
      $categories = $con->query('select * from category');

      // if we have an ID, get the game record from the database and initialize
      // a game object
      if ($id) {
        $q = 'select g.game_id, 
            g.beaten, 
            g.release_year, 
            g.title, 
            g.system_id, 
            g.developer_id, 
            group_concat(category_id) as categories
          from game g 
          left join game_category gc on g.game_id = gc.game_id
          where g.game_id = ?
          group by g.game_id, 
            g.beaten, 
            g.release_year, 
            g.title, 
            g.system_id, 
            g.developer_id';
        $stmt = $con->prepare($q);
        $stmt->bind_param('i', $id);

        if (!$stmt->execute()) {
          throw new \Exception($stmt->error);
        }
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $game = new self(
          $row['game_id'], 
          $row['title'], 
          $row['release_year'],
          $row['beaten'], 
          $row['system_id'], 
          $row['developer_id'], 
          explode(',', $row['categories'])
        );
      } else {
        // otherwise initialize a default game object (will not have an ID)
        $game = new self();
      }

      return [
        $developers,
        $systems,
        $categories,
        $game,
      ];
    } catch (\Throwable $e) {
      echo '<p>Error: ' . $e->getMessage() . '</p>';
    }
  }

  public static function initIndex(&$con) {
    $q = "select g.game_id, 
        g.beaten, 
        g.release_year, 
        g.title, 
        d.developer_id, 
        d.developer_name, 
        s.system_id, 
        s.system_name,
        group_concat(c.category_desc separator ', ') as categories
      from game g
      left join developer d on g.developer_id = d.developer_id
      left join system s on g.system_id = s.system_id
      left join game_category gc on g.game_id = gc.game_id
      left join category c on gc.category_id = c.category_id
      group by g.game_id, 
        g.beaten, 
        g.release_year, 
        g.title, 
        d.developer_id, 
        d.developer_name, 
        s.system_id, 
        s.system_name
      order by g.title";
    
    $result = $con->query($q);

    return $result && $result->num_rows > 0 ? $result : [];
  }

  /**
   * A simple function to determine whether we have a game ID or not on this
   * game object.
   */
  private function haveGameId() {
    return isset($this->id) && is_numeric($this->id);
  }

  /**
   * In PHP, we are using true or false for completed, but when we save to the
   * database, we need 1 or 0. This function does that conversion for us.
   */
  private function beatenAsInt() {
    return $this->beaten ? 1 : 0;
  }
}
