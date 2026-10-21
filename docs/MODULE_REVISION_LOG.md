# Module revision log

## DOC-03-001 — Database schema derived from the Module 3 ERD

The module refers to a SQL baseline under `references/00_database`, but that
baseline is not present in the supplied project. The Module 3 migrations are
therefore derived from the module's table and constraint instructions. The
data dictionary records this limitation; it does not claim a byte-for-byte
comparison with missing SQL files.

## DOC-03-002 — Re-runnable demo seeder

The demo seeder writes identity columns and scoped foreign keys explicitly via
`forceFill`. Some of those attributes are intentionally guarded on the models,
so treating them as `firstOrNew` lookup criteria alone would leave new rows
without required tenant identifiers. Re-running the seeder updates the same
natural-key rows instead of creating duplicates.
