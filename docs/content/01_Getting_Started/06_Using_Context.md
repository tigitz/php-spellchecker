# Using Context

You might have seen a `array $context` parameter numerous time in different
class involved in the spellchecking process.

In a **pipeline** logic execution like the `MispellingFinder` where every step are
isolated from the other and don't share any state:
```
Source -> Texts -> Text Processor -> Spellchecker -> Misspellings Handler
```

You might need a way to pass an information from the **Source** step to the 
**Misspellings Handler** for your use case.

An example could be if you want to spellcheck your blog articles (the Source) and 
want to send email containing the misspellings found and the related article in which they've been found (Mispellings Handler).
In that case, the **Source** can pass the article it's processing in the `$context` 
up through the **Spellchecker** that will attach the context to the misspellings 
found and all the way to the **Misspellings Handler**

An implementation of this use case can be found in [Create Custom Misspellings Handlers](../04_Misspellings_Handlers/10_Create_Custom.md).
