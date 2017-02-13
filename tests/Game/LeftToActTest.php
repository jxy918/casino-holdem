<?php

namespace xLink\Tests\Game;

use Ramsey\Uuid\Uuid;
use xLink\Poker\Client;
use xLink\Poker\Game\CashGame;
use xLink\Poker\Game\Chips;
use xLink\Poker\Game\Game;
use xLink\Poker\Game\LeftToAct;
use xLink\Poker\Game\Round;
use xLink\Poker\Table;

class LeftToActTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function can_create_collection_with_player_collection()
    {
        $game = $this->createGenericGame(4);

        $leftToAct = LeftToAct::make([])->setup($game->players());

        $expected = LeftToAct::make([
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);
    }

    /** @test */
    public function can_create_collection_with_with_dealer_being_last()
    {
        $game = $this->createGenericGame(4);

        $leftToAct = LeftToAct::make([])->setupWithoutDealer($game->players());

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);
    }

    /** @test */
    public function can_move_player_to_last_in_queue()
    {
        $game = $this->createGenericGame(4);

        /** @var Table $table */
        $table = $game->tables()->first();

        $leftToAct = LeftToAct::make([])
            ->setup($table->playersSatDown());

        $expected = LeftToAct::make([
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);

        $leftToAct = $leftToAct->movePlayerToLastInQueue();

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);
    }

    /** @test */
    public function can_reset_player_list_from_seat_number()
    {
        $game = $this->createGenericGame(4);

        /** @var Table $table */
        $table = $game->tables()->first();

        $leftToAct = LeftToAct::make([])
            ->setup($table->playersSatDown());

        $expected = LeftToAct::make([
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);

        $leftToAct = $leftToAct->resetPlayerListFromSeat(3);

        $expected = LeftToAct::make([
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);
    }

    /** @test */
    public function can_set_player_activity()
    {
        $game = $this->createGenericGame(4);

        /** @var Table $table */
        $table = $game->tables()->first();
        $seat1 = $table->playersSatDown()->get(0);

        $leftToAct = LeftToAct::make([])
            ->setup($table->playersSatDown());

        $expected = LeftToAct::make([
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);

        $leftToAct = $leftToAct->setActivity($seat1->name(), LeftToAct::ACTIONED);

        $expected = LeftToAct::make([
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::ACTIONED],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);
    }

    /** @test */
    public function player_can_action()
    {
        $game = $this->createGenericGame(4);

        /** @var Table $table */
        $table = $game->tables()->first();
        $seat1 = $table->playersSatDown()->get(0);

        $leftToAct = LeftToAct::make([])
            ->setup($table->playersSatDown());

        $expected = LeftToAct::make([
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);

        $leftToAct = $leftToAct->playerHasActioned($seat1, LeftToAct::ACTIONED);

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::ACTIONED],
        ]);
        $this->assertEquals($expected, $leftToAct);
    }

    /** @test */
    public function player_can_aggressively_action()
    {
        $game = $this->createGenericGame(4);

        /** @var Table $table */
        $table = $game->tables()->first();
        $seat1 = $table->playersSatDown()->get(0);

        $leftToAct = LeftToAct::make([])
            ->setup($table->playersSatDown());

        $expected = LeftToAct::make([
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);

        $leftToAct = $leftToAct->playerHasActioned($seat1, LeftToAct::AGGRESSIVELY_ACTIONED);

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::AGGRESSIVELY_ACTIONED],
        ]);
        $this->assertEquals($expected, $leftToAct);
    }

    /** @test */
    public function checks_leftToAct_throughout_a_complete_round()
    {
        $game = $this->createGenericGame(4);

        $table = $game->tables()->first();

        $player1 = $table->playersSatDown()->get(0);
        $player2 = $table->playersSatDown()->get(1);
        $player3 = $table->playersSatDown()->get(2);
        $player4 = $table->playersSatDown()->get(3);

        $round = Round::start($table);

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        // deal some hands
        $round->dealHands();

        $round->postSmallBlind($player2); // 25
        $round->postBigBlind($player3); // 50

        $round->playerCalls($player4); // 50
        $round->playerFoldsHand($player1);
        $round->playerCalls($player2); // SB + 25
        $round->playerChecks($player3); // BB

        $expected = LeftToAct::make([
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::ACTIONED],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::ACTIONED],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::ACTIONED],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        // collect the chips, burn a card, deal the flop
        $round->dealFlop();

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        $round->playerChecks($player2); // 0
        $round->playerRaises($player3, Chips::fromAmount(250)); // 250
        $round->playerCalls($player4); // 250
        $round->playerFoldsHand($player2);

        $expected = LeftToAct::make([
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::AGGRESSIVELY_ACTIONED],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::ACTIONED],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        // collect chips, burn 1, deal 1
        $round->dealTurn();

        $expected = LeftToAct::make([
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        /*$round->playerRaises($player3, Chips::fromAmount(450)); // 450
        $round->playerCalls($player4); // 450

        // collect chips, burn 1, deal 1
        // var_dump($round->leftToAct()->map(function ($player) { return implode(' / ', $player); }));
        $round->dealRiver();

        // var_dump($round->leftToAct()->map(function ($player) { return implode(' / ', $player); }));

        $round->playerPushesAllIn($player3); // 250
        $round->playerCalls($player4); // 250
        // var_dump($round->leftToAct()->map(function ($player) { return implode(' / ', $player); }));

        $round->end();*/
    }

    /** @test */
    public function when_the_dealer_starts_the_new_betting_round_with_two_players_the_first_player_to_act_is_the_small_blind()
    {
        $game = $this->createGenericGame(2);

        /** @var Table $table */
        $table = $game->tables()->first();

        $round = Round::start($table);

        $seat1 = $table->playersSatDown()->get(0);
        $seat2 = $table->playersSatDown()->get(1);

        $round->playerChecks($seat1);
        $round->playerChecks($seat2);

        // Deal flop
        $round->dealFlop();

        $this->assertEquals($game->players()->get(0), $round->whosTurnIsIt());
    }

    /** @test */
    public function actioned_player_gets_pushed_to_last_place_on_leftToAct_collection()
    {
        $game = $this->createGenericGame(9);

        /** @var Table $table */
        $table = $game->tables()->first();

        $seat2 = $table->playersSatDown()->get(1);

        $round = Round::start($table);

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 4, 'player' => 'player5', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 5, 'player' => 'player6', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 6, 'player' => 'player7', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 7, 'player' => 'player8', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 8, 'player' => 'player9', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        $round->playerCalls($seat2);

        $expected = LeftToAct::make([
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 4, 'player' => 'player5', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 5, 'player' => 'player6', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 6, 'player' => 'player7', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 7, 'player' => 'player8', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 8, 'player' => 'player9', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::ACTIONED],
        ]);
        $this->assertEquals($expected, $round->leftToAct());
    }

    /** @test */
    public function aggressive_action_resets_all_actions()
    {
        $game = $this->createGenericGame(6);

        /** @var Table $table */
        $table = $game->tables()->first();

        $seat1 = $table->playersSatDown()->get(0);
        $seat2 = $table->playersSatDown()->get(1);
        $seat3 = $table->playersSatDown()->get(2);
        $seat4 = $table->playersSatDown()->get(3);
        $seat5 = $table->playersSatDown()->get(4);
        $seat6 = $table->playersSatDown()->get(5);

        $round = Round::start($table);

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 4, 'player' => 'player5', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 5, 'player' => 'player6', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        $round->postSmallBlind($seat2);
        $round->postBigBlind($seat3);

        $round->playerCalls($seat4);
        $round->playerCalls($seat5);
        $round->playerCalls($seat6);
        $round->playerPushesAllIn($seat1);

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 4, 'player' => 'player5', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 5, 'player' => 'player6', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::AGGRESSIVELY_ACTIONED],
        ]);

        $this->assertEquals($expected, $round->leftToAct());
    }

    /** @test */
    public function fold_action_gets_players_removed_from_leftToAct()
    {
        $game = $this->createGenericGame(6);

        /** @var Table $table */
        $table = $game->tables()->first();

        $seat1 = $table->playersSatDown()->get(0);
        $seat2 = $table->playersSatDown()->get(1);
        $seat3 = $table->playersSatDown()->get(2);
        $seat4 = $table->playersSatDown()->get(3);
        $seat5 = $table->playersSatDown()->get(4);
        $seat6 = $table->playersSatDown()->get(5);

        $round = Round::start($table);

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 4, 'player' => 'player5', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 5, 'player' => 'player6', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        $round->postSmallBlind($seat2);
        $round->postBigBlind($seat3);

        $round->playerCalls($seat4);
        $round->playerFoldsHand($seat5);

        $expected = LeftToAct::make([
            ['seat' => 5, 'player' => 'player6', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::SMALL_BLIND],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::BIG_BLIND],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::ACTIONED],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        $round->playerCalls($seat6);
        $round->playerPushesAllIn($seat1);

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 5, 'player' => 'player6', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::AGGRESSIVELY_ACTIONED],
        ]);
        $this->assertEquals($expected, $round->leftToAct());
    }

    /**
     * @param int $playerCount
     *
     * @return Game
     */
    private function createGenericGame($playerCount = 4): Game
    {
        $players = [];
        for ($i = 0; $i < $playerCount; ++$i) {
            $players[] = Client::register('player'.($i + 1), Chips::fromAmount(5500));
        }

        // we got a game
        $game = CashGame::setUp(Uuid::uuid4(), 'Demo Cash Game', Chips::fromAmount(500));

        // register clients to game
        foreach ($players as $player) {
            $game->registerPlayer($player, Chips::fromAmount(1000));
        }

        $game->assignPlayersToTables(); // table has max of 9 or 5 players in holdem

        return $game;
    }
}
