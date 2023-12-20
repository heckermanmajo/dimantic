# Code Quality

## Styleguide

## Unit-Tests

## DB-persistence-tests
Check that all data in the db is correct.
-> check all ids, and values are correct
This can be done after a backup is loaded 
to the local machine.

Use attributes(id to class-xyz) and the check function for this.
 
    #[Ref(to="ClassName")]
    #[NotBiggerThan(100)]
    #[NotSmallerThan(0)]
    #[NotEqual(0)]

and the check function of dataclass.