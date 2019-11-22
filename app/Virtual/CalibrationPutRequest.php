<?php


/**
 *  @OA\Schema(
 *      schema="Error",
 *      required={"message"},
 *      @OA\Property(
 *          property="errors",
 *          type="string",
 *          example = " Sorry, something went wrong... Unauthenticated."
 *      )
 *  ),
 *  @OA\Schema(
 *      schema="itemsTrackerSession",
 *      @OA\Property(
 *          property="items",
 *          ref="#/components/schemas/TrackerSession",
 *      ),
 *  ),
 */

