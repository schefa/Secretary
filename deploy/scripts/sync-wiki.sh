#!/usr/bin/env bash
# Mirrors docs/ onto the GitHub wiki (a separate repo: <origin>.wiki.git).
#
# docs/ is the single source of truth - this does a full mirror (wipes the
# wiki checkout except .git before recopying) rather than an incremental
# sync, so a page renamed/removed in docs/ doesn't leave an orphan behind.
# Consequence: any edits made directly on the wiki via GitHub's web UI get
# overwritten on the next sync.
#
# Auth: locally this pushes over plain https, relying on your existing git
# credentials (same as any other push to GitHub). In CI, set GH_TOKEN (a
# token with contents:write on this repo) and it's used instead.
set -euo pipefail
cd "$(git rev-parse --show-toplevel)"

REPO_URL="$(git remote get-url origin | sed -E 's#\.git$##; s#^git@github\.com:#https://github.com/#')"
WIKI_HTTPS="${REPO_URL}.wiki.git"

if [ -n "${WIKI_REMOTE:-}" ]; then
	: # explicit override (e.g. for testing against a throwaway repo)
elif [ -n "${GH_TOKEN:-}" ]; then
	WIKI_REMOTE="$(echo "$WIKI_HTTPS" | sed -E "s#https://#https://x-access-token:${GH_TOKEN}@#")"
else
	WIKI_REMOTE="$WIKI_HTTPS"
fi

WORKDIR="$(mktemp -d)"
trap 'rm -rf "$WORKDIR"' EXIT

git clone --quiet "$WIKI_REMOTE" "$WORKDIR/wiki"

find "$WORKDIR/wiki" -mindepth 1 -maxdepth 1 -not -name '.git' -exec rm -rf {} +

cp docs/index.md "$WORKDIR/wiki/Home.md"

for f in docs/*.md; do
	base="$(basename "$f")"
	[ "$base" = "index.md" ] && continue
	cp "$f" "$WORKDIR/wiki/$base"
done

cp -r docs/images "$WORKDIR/wiki/"
cp deploy/scripts/wiki-sidebar.md "$WORKDIR/wiki/_Sidebar.md"

# Links that only resolve inside the main repo's own tree - the wiki is a
# separate repo, so these need to become absolute GitHub URLs instead.
sed -i.bak \
	-e "s#\[Dockerfile\](\.\./Dockerfile)#[Dockerfile](${REPO_URL}/blob/master/Dockerfile)#" \
	-e "s#\[project README\](\.\./README\.md)#[project README](${REPO_URL}/blob/master/README.md)#" \
	"$WORKDIR"/wiki/*.md

# index.md became Home.md above - repoint every link that assumed otherwise.
sed -i.bak 's#\](index\.md)#](Home.md)#g' "$WORKDIR"/wiki/*.md

find "$WORKDIR/wiki" -name '*.bak' -delete

cd "$WORKDIR/wiki"
git add -A

if git diff --cached --quiet; then
	echo "Wiki already up to date."
	exit 0
fi

git -c user.name="${GIT_AUTHOR_NAME:-github-actions[bot]}" \
	-c user.email="${GIT_AUTHOR_EMAIL:-github-actions[bot]@users.noreply.github.com}" \
	commit --quiet -m "Sync docs/ to wiki"
git push --quiet origin HEAD:master

echo "Wiki updated."
