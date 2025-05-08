## todo
- make router comply with start/end dates of edges
- Protectorate
- Helper suite
- Test double edges

## Formulas

### Haversine

$$
a = \sin^2\left(\frac{\Delta\varphi}{2}\right) + \cos\varphi_1 \cos\varphi_2 \sin^2\left(\frac{\Delta\lambda}{2}\right)
$$

$$
c = 2 \cdot \text{atan2}\left(\sqrt{a}, \sqrt{1-a}\right)
$$

$$
d = R \cdot c
$$

Where:
- $\varphi_1, \varphi_2$ are the latitudes in radians
- $\lambda_1, \lambda_2$ are the longitudes in radians
- $\Delta\varphi = \varphi_2 - \varphi_1$
- $\Delta\lambda = \lambda_2 - \lambda_1$
- $R$ is the Earth's radius (defined in km so result in km)
- $a$ is the square of half the chord length between the points
- $c$ is the angular distance in radians
- $d$ is the distance between the points

### Spherical Law of Cosines

$$
d = R \cdot \arccos\left(\sin \varphi_1 \sin \varphi_2 + \cos \varphi_1 \cos \varphi_2 \cos \Delta \lambda \right)
$$

Where:
- $\varphi_1, \varphi_2$ are the latitudes of the two points in radians,
- $\lambda_1, \lambda_2$ are the longitudes in radians,
- $\Delta\lambda = \lambda_2 - \lambda_1$ is the difference in longitude,
- $R$ is the Earth's radius (typically 6371 km),
- $d$ is the great-circle distance between the points.

### Bearing

$$
\theta = \text{atan2}\left(\sin \Delta\lambda \cdot \cos \varphi_2, \cos \varphi_1 \cdot \sin \varphi_2 - \sin \varphi_1 \cdot \cos \varphi_2 \cdot \cos \Delta\lambda\right)
$$

Where:
- $\varphi_1, \varphi_2$ are the latitudes in radians,
- $\lambda_1, \lambda_2$ are the longitudes in radians,
- $\Delta\lambda = \lambda_2 - \lambda_1$ is the difference in longitude,
- $\theta$ is the bearing in radians (0 = North, π/2 = East, π = South, 3π/2 = West).

### Turn Angle

$$
\text{Angle} = |\theta_2 - \theta_1|
$$

If the result is greater than 180°, normalize it:

$$
\text{Angle} = 360° - \text{Angle}
$$

Where:
- $\theta_1$ is the bearing from the previous point to the current point,
- $\theta_2$ is the bearing from the current point to the next point,
- The result is the angle in degrees (0-180°, where 0 = straight line, 180° = U-turn).


## Overly Strict Skipping Logic

- What: The original condition if ($gScore[currentID] < insertion_gScore$) skipped nodes when their gScore equaled the insertion_gScore, preventing exploration of valid paths (e.g., DC_ANTWERP or AIR_EDDM).
- Why: A* should process nodes with equal gScores since they’re still optimal paths, but this condition pruned them prematurely.
- Resolution: Changed to if ($gScore[currentID] <= insertion_{gScore}$) to include equal gScores.

## Floating-Point Imprecision

- What: Even with $<=$, tiny floating-point differences (e.g., $30.097405112933$ vs. $30.097405112932$) caused equal gScores to be treated as unequal, still triggering skips.
- Why: Recomputing $insertion_gScore$ $(fScore - h)$ introduced precision errors from $getDistanceTo$, making <= insufficiently robust.
- Resolution: Added an epsilon tolerance $(gScore[currentID] - insertion_{gScore} <= epsilon)$ to handle these near-equal comparisons.



## Package model refactor

| Method               | Issue                                       | Improvement                                                     | Impact                              |
|----------------------|---------------------------------------------|-----------------------------------------------------------------|-------------------------------------|
| `getNextMovement`    | Multiple database queries, redundant checks | Load movements once, use collection methods                     | Reduced queries, improved speed     |
| `move`               | Long, nested conditionals                   | Split into `deliverPackage` and `performMovementOperation`      | Better readability, maintainability |
| `fakeMove`           | Nested conditionals, complex logic          | Use timestamp list, handle address case separately              | Simplified logic                    |
| `getCurrentMovement` | Try-catch block, redundant checks           | Simplified with helper method, removed broad exception handling | Much more robust                    |

