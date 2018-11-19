/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
function ttToggleRides(rankingID) {
    var element = document.getElementById("tt-rankings-" + rankingID + "-rides");
    if (element.style.display === "none") {
        element.style.display = "table-row";
    } else {
        element.style.display = "none";
    }
    var element = document.getElementById("tt-rankings-row-" + rankingID + "-icon");
    element.classList.toggle("fa-angle-right");
    element.classList.toggle("fa-angle-down");
}