## Upcoming Release

- Overhaul of testing suite, improving code coverage percentage and providing additional checks on core features.
- `User->priorityAsOf()` will now return a 0-term `Priority` object if an invalid term ID is passed in.
- Users with the "auto-request Zone S" permission or Board role can now automatically generate a Board Upgrade Certificate upon joining an eligible show, in addition to when they create one themselves.
- Users who have completed onboarding will now be redirected to the profile screen instead of the home screen if they attempt to go back to the onboarding screen.
- Show-specific permissions are now enforced; users must be either a member of the Board or a host of a show in order to see its details.
- Users will no longer see the Join Show screen if they attempt to join a show that they're already a part of.
- Guest accounts (those with email addresses not ending in `@carleton.edu`) no longer see any options related to creating shows on their home screen, and no longer have permission to complete any show-related actions.
- Improvements to the profanity filters - they now catch additional words.

Notes to developers:

- Almost all tests have been replaced to improve code coverage.
- Factory definitions and states have been tweaked and added. The only state change is that the "active" flag is now set to true on the Track factory class. Other states have been added to address common considerations in testing.
